import React from 'react';
import { Head, Link, usePage, useForm, router } from '@inertiajs/react';
import Header from '@/components/header';
import { format } from 'date-fns';
import { nl } from 'date-fns/locale';
import Layout from '@/components/Layout';

interface ActivityRegistration {
    id: number;
    status: string;
    registered_at: string;
    paid_amount: number | null;
    payment_confirmed: boolean;
    user: {
        id: number;
        name: string;
    };
}

interface Activity {
    id: number;
    title: string;
    slug: string;
    description: string;
    location: string;
    start_date: string;
    end_date: string;
    start_time: string | null;
    end_time: string | null;
    type: string;
    status: string;
    max_participants: number | null;
    current_participants: number;
    entry_fee: number | null;
    requires_registration: boolean;
    registration_deadline: string | null;
    requirements: string | null;
    contact_info: string | null;
    featured_image: string | null;
    additional_info: any;
    organizer: {
        id: number;
        name: string;
    };
    registrations: ActivityRegistration[];
}

interface PageProps {
    activity: Activity;
    userRegistration: ActivityRegistration | null;
    [key: string]: any;
}

export default function ActivityDetail() {
    const { activity, userRegistration, auth } = usePage<PageProps>().props;

    const { post, delete: destroy, processing } = useForm();

    const formatDate = (dateString: string) => {
        try {
            return format(new Date(dateString), 'd MMMM yyyy', { locale: nl });
        } catch {
            return dateString;
        }
    };

    const formatDateTime = (dateString: string, timeString: string | null) => {
        try {
            const date = format(new Date(dateString), 'd MMMM yyyy', { locale: nl });
            return timeString ? `${date} om ${timeString}` : date;
        } catch {
            return dateString;
        }
    };

    const getStatusBadge = (status: string) => {
        switch (status) {
            case 'gepland':
                return <span className="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">Gepland</span>;
            case 'bevestigd':
                return <span className="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Bevestigd</span>;
            case 'geannuleerd':
                return <span className="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">Geannuleerd</span>;
            case 'afgelopen':
                return <span className="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded">Afgelopen</span>;
            default:
                return <span className="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded">{status}</span>;
        }
    };

    const getTypeBadge = (type: string) => {
        const typeLabels: Record<string, string> = {
            'training': 'Training',
            'wedstrijd': 'Wedstrijd',
            'social': 'Social',
            'vergadering': 'Vergadering',
            'overig': 'Overig'
        };

        const typeColors: Record<string, string> = {
            'training': 'bg-purple-100 text-purple-800',
            'wedstrijd': 'bg-orange-100 text-orange-800',
            'social': 'bg-pink-100 text-pink-800',
            'vergadering': 'bg-indigo-100 text-indigo-800',
            'overig': 'bg-gray-100 text-gray-800'
        };

        return (
            <span className={`${typeColors[type] || typeColors.overig} text-xs font-medium px-2.5 py-0.5 rounded`}>
                {typeLabels[type] || type}
            </span>
        );
    };

    const canRegister = () => {
        if (!activity.requires_registration) return false;
        if (userRegistration) return false;
        if (!auth.user) return false;
        if (activity.status === 'geannuleerd' || activity.status === 'afgelopen') return false;
        if (activity.registration_deadline && new Date() > new Date(activity.registration_deadline)) return false;
        if (activity.max_participants && activity.current_participants >= activity.max_participants) return false;
        return true;
    };

    const canUnregister = () => {
        return userRegistration && activity.status !== 'afgelopen' && activity.status !== 'geannuleerd';
    };

    const handleRegister = () => {
        post(`/activiteiten/${activity.slug}/aanmelden`, {
            preserveScroll: true,
        });
    };

    const handleUnregister = () => {
        if (confirm('Weet je zeker dat je je wilt afmelden voor deze activiteit?')) {
            destroy(`/activiteiten/${activity.slug}/afmelden`, {
                preserveScroll: true,
            });
        }
    };

    const isRegistrationFull = activity.max_participants && activity.current_participants >= activity.max_participants;
    const isRegistrationExpired = activity.registration_deadline && new Date() > new Date(activity.registration_deadline);

    return (
        <Layout>
            <Head title={activity.title} />
            
            
            <div className="w-[90%] mx-auto px-4 py-8">
                {/* Breadcrumb */}
                <nav className="mb-8">
                    <ol className="flex items-center space-x-2 text-sm text-gray-500">
                        <li>
                            <Link href="/activiteiten" className="hover:text-blue-600">
                                Activiteiten
                            </Link>
                        </li>
                        <li>
                            <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fillRule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clipRule="evenodd" />
                            </svg>
                        </li>
                        <li className="text-gray-900">{activity.title}</li>
                    </ol>
                </nav>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {/* Main Content */}
                    <div className="lg:col-span-2">
                        {/* Hero Image */}
                        {activity.featured_image && (
                            <div className="mb-6">
                                <img
                                    src={activity.featured_image}
                                    alt={activity.title}
                                    className="w-full h-64 object-cover rounded-lg"
                                />
                            </div>
                        )}

                        {/* Title and Badges */}
                        <div className="mb-6">
                            <div className="flex items-center space-x-2 mb-2">
                                {getTypeBadge(activity.type)}
                                {getStatusBadge(activity.status)}
                            </div>
                            <h1 className="text-3xl font-bold text-gray-900 mb-4">{activity.title}</h1>
                        </div>

                        {/* Description */}
                        <div className="mb-8">
                            <h2 className="text-xl font-semibold text-gray-900 mb-4">Beschrijving</h2>
                            <div className="prose max-w-none">
                                <p className="text-gray-700 whitespace-pre-line">{activity.description}</p>
                            </div>
                        </div>

                        {/* Requirements */}
                        {activity.requirements && (
                            <div className="mb-8">
                                <h2 className="text-xl font-semibold text-gray-900 mb-4">Vereisten</h2>
                                <p className="text-gray-700 whitespace-pre-line">{activity.requirements}</p>
                            </div>
                        )}

                        {/* Additional Info */}
                        {activity.additional_info && Object.keys(activity.additional_info).length > 0 && (
                            <div className="mb-8">
                                <h2 className="text-xl font-semibold text-gray-900 mb-4">Aanvullende Informatie</h2>
                                <div className="space-y-3">
                                    {Object.entries(activity.additional_info).map(([key, value]) => (
                                        <div key={key} className="flex flex-col">
                                            <span className="text-sm font-medium text-gray-700">
                                                {key.split('_')
                                                    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                                                    .join(' ')}
                                            </span>
                                            <span className="text-gray-600">{
                                                typeof value === 'boolean' 
                                                    ? (value ? 'Ja' : 'Nee')
                                                    : String(value)
                                            }</span>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        )}

                        {/* Contact Info */}
                        {activity.contact_info && (
                            <div className="mb-8">
                                <h2 className="text-xl font-semibold text-gray-900 mb-4">Contact</h2>
                                <p className="text-gray-700 whitespace-pre-line">{activity.contact_info}</p>
                            </div>
                        )}
                    </div>

                    {/* Sidebar */}
                    <div className="lg:col-span-1">
                        <div className="bg-white rounded-lg shadow-md p-6 sticky top-4">
                            {/* Date and Time */}
                            <div className="mb-6">
                                <h3 className="text-lg font-semibold text-gray-900 mb-3">Details</h3>
                                <div className="space-y-3 text-sm">
                                    <div className="flex items-center">
                                        <svg className="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <div>
                                            <div className="font-medium">Start</div>
                                            <div className="text-gray-600">{formatDateTime(activity.start_date, activity.start_time)}</div>
                                        </div>
                                    </div>

                                    {activity.end_date && (
                                        <div className="flex items-center">
                                            <svg className="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <div>
                                                <div className="font-medium">Einde</div>
                                                <div className="text-gray-600">{formatDateTime(activity.end_date, activity.end_time)}</div>
                                            </div>
                                        </div>
                                    )}

                                    <div className="flex items-center">
                                        <svg className="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <div>
                                            <div className="font-medium">Locatie</div>
                                            <div className="text-gray-600">{activity.location}</div>
                                        </div>
                                    </div>

                                    <div className="flex items-center">
                                        <svg className="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <div>
                                            <div className="font-medium">Organisator</div>
                                            <div className="text-gray-600">{activity.organizer.name}</div>
                                        </div>
                                    </div>

                                    {activity.entry_fee && activity.entry_fee > 0 && (
                                        <div className="flex items-center">
                                            <svg className="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                            </svg>
                                            <div>
                                                <div className="font-medium">Kosten</div>
                                                <div className="text-gray-600">€{activity.entry_fee}</div>
                                            </div>
                                        </div>
                                    )}
                                </div>
                            </div>

                            {/* Registration Info */}
                            {activity.requires_registration && (
                                <div className="mb-6">
                                    <h3 className="text-lg font-semibold text-gray-900 mb-3">Aanmelding</h3>
                                    <div className="space-y-3 text-sm">
                                        <div className="flex items-center justify-between">
                                            <span className="text-gray-600">Deelnemers</span>
                                            <span className={`font-medium ${isRegistrationFull ? 'text-red-600' : 'text-green-600'}`}>
                                                {activity.current_participants}
                                                {activity.max_participants && `/${activity.max_participants}`}
                                            </span>
                                        </div>

                                        {activity.registration_deadline && (
                                            <div className="flex items-center justify-between">
                                                <span className="text-gray-600">Aanmelddeadline</span>
                                                <span className={`font-medium ${isRegistrationExpired ? 'text-red-600' : 'text-gray-900'}`}>
                                                    {formatDate(activity.registration_deadline)}
                                                </span>
                                            </div>
                                        )}

                                        {userRegistration && (
                                            <div className="bg-green-50 border border-green-200 rounded-lg p-3">
                                                <div className="flex items-center">
                                                    <svg className="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                                                    </svg>
                                                    <span className="text-green-800 font-medium">Je bent aangemeld</span>
                                                </div>
                                            </div>
                                        )}
                                    </div>
                                </div>
                            )}

                            {/* Registration Actions */}
                            {activity.requires_registration && auth.user && (
                                <div className="space-y-3">
                                    {canRegister() && (
                                        <button
                                            onClick={handleRegister}
                                            disabled={processing}
                                            className="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50"
                                        >
                                            {processing ? 'Aanmelden...' : 'Aanmelden'}
                                        </button>
                                    )}

                                    {canUnregister() && (
                                        <button
                                            onClick={handleUnregister}
                                            disabled={processing}
                                            className="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50"
                                        >
                                            {processing ? 'Afmelden...' : 'Afmelden'}
                                        </button>
                                    )}

                                    {!canRegister() && !userRegistration && (
                                        <div className="text-center text-sm text-gray-600 p-3 bg-gray-50 rounded-lg">
                                            {isRegistrationFull && 'Deze activiteit is vol'}
                                            {isRegistrationExpired && 'Aanmeldperiode is verlopen'}
                                            {activity.status === 'geannuleerd' && 'Deze activiteit is geannuleerd'}
                                            {activity.status === 'afgelopen' && 'Deze activiteit is afgelopen'}
                                        </div>
                                    )}
                                </div>
                            )}

                            {activity.requires_registration && !auth.user && (
                                <div className="text-center">
                                    <Link
                                        href="/login"
                                        className="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors inline-block"
                                    >
                                        Log in om aan te melden
                                    </Link>
                                </div>
                            )}

                            {!activity.requires_registration && (
                                <div className="text-center text-sm text-gray-600 p-3 bg-gray-50 rounded-lg">
                                    Geen aanmelding vereist
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
