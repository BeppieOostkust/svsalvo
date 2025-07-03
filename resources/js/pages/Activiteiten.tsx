import React from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import Header from '@/components/header';
import { format } from 'date-fns';
import { nl } from 'date-fns/locale';
import Layout from '@/components/Layout';

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
    featured_image: string | null;
    organizer: {
        id: number;
        name: string;
    };
}

interface PageProps {
    upcomingActivities: Activity[];
    pastActivities: Activity[];
    [key: string]: any;
}

export default function Activiteiten() {
    const { upcomingActivities, pastActivities } = usePage<PageProps>().props;

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

    const getParticipantsInfo = (activity: Activity) => {
        if (!activity.requires_registration) {
            return <span className="text-gray-600">Geen aanmelding vereist</span>;
        }

        if (activity.max_participants) {
            return (
                <span className={`${activity.current_participants >= activity.max_participants ? 'text-red-600' : 'text-green-600'}`}>
                    {activity.current_participants}/{activity.max_participants} deelnemers
                </span>
            );
        }

        return <span className="text-green-600">{activity.current_participants} deelnemers</span>;
    };

    return (
        <Layout>
            <Head title="Activiteiten" />
            
            
            <div className="w-[90%] mx-auto px-4 py-8">
                {/* Header */}
                <div className="mb-8">
                    <h1 className="text-4xl font-bold text-gray-900 mb-4">Activiteiten</h1>
                    <p className="text-xl text-gray-600">
                        Ontdek alle aankomende activiteiten en evenementen van onze schietvereniging.
                    </p>
                </div>

                {/* Upcoming Activities */}
                <div className="mb-12">
                    <h2 className="text-2xl font-bold text-gray-900 mb-6">Aankomende Activiteiten</h2>
                    
                    {upcomingActivities.length === 0 ? (
                        <div className="text-center py-12">
                            <div className="text-gray-400 mb-4">
                                <svg className="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h3 className="text-lg font-medium text-gray-900 mb-2">Geen aankomende activiteiten</h3>
                            <p className="text-gray-600">Er zijn momenteel geen geplande activiteiten.</p>
                        </div>
                    ) : (
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            {upcomingActivities.map((activity) => (
                                <div key={activity.id} className="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                                    {activity.featured_image && (
                                        <img
                                            src={activity.featured_image}
                                            alt={activity.title}
                                            className="w-full h-48 object-cover"
                                        />
                                    )}
                                    <div className="p-6">
                                        <div className="flex items-center justify-between mb-2">
                                            {getTypeBadge(activity.type)}
                                            {getStatusBadge(activity.status)}
                                        </div>
                                        
                                        <h3 className="text-lg font-bold text-gray-900 mb-2">
                                            <Link
                                                href={`/activiteiten/${activity.slug}`}
                                                className="hover:text-blue-600 transition-colors"
                                            >
                                                {activity.title}
                                            </Link>
                                        </h3>
                                        
                                        <div 
                                            className="text-gray-600 mb-4 line-clamp-3"
                                            dangerouslySetInnerHTML={{ __html: activity.description }}
                                        />
                                        
                                        <div className="space-y-2 text-sm text-gray-600 mb-4">
                                            <div className="flex items-center">
                                                <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                {formatDateTime(activity.start_date, activity.start_time)}
                                            </div>
                                            
                                            <div className="flex items-center">
                                                <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                {activity.location}
                                            </div>
                                            
                                            <div className="flex items-center">
                                                <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 0a4 4 0 11-8-2.828" />
                                                </svg>
                                                {getParticipantsInfo(activity)}
                                            </div>
                                            
                                            {activity.entry_fee && activity.entry_fee > 0 && (
                                                <div className="flex items-center">
                                                    <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                                    </svg>
                                                    €{activity.entry_fee}
                                                </div>
                                            )}
                                        </div>
                                        
                                        <div className="flex justify-between items-center">
                                            <span className="text-sm text-gray-500">
                                                Door {activity.organizer.name}
                                            </span>
                                            <Link
                                                href={`/activiteiten/${activity.slug}`}
                                                className="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm"
                                            >
                                                Meer info
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}
                </div>

                {/* Past Activities */}
                {pastActivities.length > 0 && (
                    <div className="mb-8">
                        <h2 className="text-2xl font-bold text-gray-900 mb-6">Recente Activiteiten</h2>
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            {pastActivities.map((activity) => (
                                <div key={activity.id} className="bg-gray-50 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow opacity-75">
                                    {activity.featured_image && (
                                        <img
                                            src={activity.featured_image}
                                            alt={activity.title}
                                            className="w-full h-48 object-cover"
                                        />
                                    )}
                                    <div className="p-6">
                                        <div className="flex items-center justify-between mb-2">
                                            {getTypeBadge(activity.type)}
                                            {getStatusBadge(activity.status)}
                                        </div>
                                        
                                        <h3 className="text-lg font-bold text-gray-900 mb-2">
                                            <Link
                                                href={`/activiteiten/${activity.slug}`}
                                                className="hover:text-blue-600 transition-colors"
                                            >
                                                {activity.title}
                                            </Link>
                                        </h3>
                                        
                                        <div 
                                            className="text-gray-600 mb-4 line-clamp-3"
                                            dangerouslySetInnerHTML={{ __html: activity.description }}
                                        />
                                        
                                        <div className="space-y-2 text-sm text-gray-600 mb-4">
                                            <div className="flex items-center">
                                                <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                {formatDateTime(activity.start_date, activity.start_time)}
                                            </div>
                                            
                                            <div className="flex items-center">
                                                <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                {activity.location}
                                            </div>
                                        </div>
                                        
                                        <div className="flex justify-between items-center">
                                            <span className="text-sm text-gray-500">
                                                Door {activity.organizer.name}
                                            </span>
                                            <Link
                                                href={`/activiteiten/${activity.slug}`}
                                                className="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors text-sm"
                                            >
                                                Bekijk
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                )}
            </div>
        </Layout>
    );
}
