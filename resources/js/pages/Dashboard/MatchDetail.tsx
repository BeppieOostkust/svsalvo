import React from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import Header from '@/components/header';
import { format } from 'date-fns';
import { nl } from 'date-fns/locale';
import { Badge } from '@/components/ui/badge';

interface User {
    id: number;
    name: string;
    first_name: string | null;
    last_name: string | null;
    show_in_participants: boolean;
}

interface Registration {
    id: number;
    user: User;
    payment_status: 'pending' | 'completed';
    created_at: string;
}

interface Match {
    id: number;
    naam: string;
    beschrijving: string | null;
    start_datum: string;
    status: string;
    registrations: Registration[];
}

interface PageProps {
    match: Match;
    isRegistered: boolean;
    userRegistration: Registration | null;
    [key: string]: any;
}

export default function MatchDetail() {
    const { match, isRegistered, userRegistration } = usePage<PageProps>().props;

    const formatDateTime = (dateString: string) => {
        try {
            return format(new Date(dateString), 'd MMMM yyyy HH:mm', { locale: nl });
        } catch {
            return dateString;
        }
    };

    const getStatusBadge = (status: string) => {
        const statusColors = {
            'binnenkort': 'bg-blue-100 text-blue-800',
            'open': 'bg-green-100 text-green-800',
            'gesloten': 'bg-red-100 text-red-800',
            'afgelopen': 'bg-gray-100 text-gray-800',
        };
        return statusColors[status as keyof typeof statusColors] || 'bg-gray-100 text-gray-800';
    };

    const getPaymentStatusBadge = (status: 'pending' | 'completed') => {
        return status === 'completed' 
            ? 'bg-green-100 text-green-800' 
            : 'bg-yellow-100 text-yellow-800';
    };

    const getPaymentStatusText = (status: 'pending' | 'completed') => {
        return status === 'completed' ? 'Betaald' : 'Nog niet betaald';
    };

    return (
        <>
            <Head title={match.naam} />
            <Header />

            <div className="w-[90%] mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div className="mb-8">
                    <div className="flex items-center justify-between">
                        <h1 className="text-3xl font-bold text-gray-900">{match.naam}</h1>
                        <Badge className={getStatusBadge(match.status)}>
                            {match.status.charAt(0).toUpperCase() + match.status.slice(1)}
                        </Badge>
                    </div>
                    <p className="mt-2 text-gray-600">
                        {formatDateTime(match.start_datum)}
                    </p>
                    {match.beschrijving && (
                        <p className="mt-4 text-gray-700">{match.beschrijving}</p>
                    )}
                </div>

                {/* Registratie Status */}
                {isRegistered && userRegistration && (
                    <div className="mb-8 bg-white rounded-lg shadow-md p-6">
                        <h2 className="text-xl font-semibold mb-4">Jouw Registratie</h2>
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-gray-600">Status</p>
                                <Badge className={getPaymentStatusBadge(userRegistration.payment_status)}>
                                    {getPaymentStatusText(userRegistration.payment_status)}
                                </Badge>
                            </div>
                            <div>
                                <p className="text-gray-600">Geregistreerd op</p>
                                <p className="font-medium">{formatDateTime(userRegistration.created_at)}</p>
                            </div>
                        </div>
                    </div>
                )}

                {/* Deelnemerslijst */}
                <div className="bg-white rounded-lg shadow-md p-6">
                    <h2 className="text-xl font-semibold mb-4">Deelnemers ({match.registrations.filter(reg => reg.user.show_in_participants).length})</h2>
                    
                    {match.registrations.length === 0 ? (
                        <p className="text-gray-500 text-center py-8">
                            Nog geen deelnemers voor deze wedstrijd.
                        </p>
                    ) : (
                        <div className="space-y-4">
                            {match.registrations
                                .filter(registration => registration.user.show_in_participants)
                                .map((registration) => (
                                    <div key={registration.id} className="flex items-center justify-between py-2">
                                        <div>
                                            <p className="font-medium">
                                                {registration.user.first_name && registration.user.last_name
                                                    ? `${registration.user.first_name} ${registration.user.last_name}`
                                                    : registration.user.name}
                                            </p>
                                        </div>
                                        <Badge className={getPaymentStatusBadge(registration.payment_status)}>
                                            {getPaymentStatusText(registration.payment_status)}
                                        </Badge>
                                    </div>
                                ))}
                        </div>
                    )}
                    
                    <p className="mt-4 text-sm text-gray-500">
                        * Alleen deelnemers die hiervoor toestemming hebben gegeven worden getoond
                    </p>
                </div>

                {/* Terug knop */}
                <div className="mt-8">
                    <Link
                        href="/dashboard"
                        className="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors"
                    >
                        <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Terug naar Dashboard
                    </Link>
                </div>
            </div>
        </>
    );
}
