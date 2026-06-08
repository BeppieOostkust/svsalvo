import React, { useState } from "react";
import { Head, Link, usePage, router } from '@inertiajs/react';
import Layout from "@/components/Layout";
import { format, parseISO } from 'date-fns';
import { nl } from 'date-fns/locale';

interface Competition {
    id: number;
    jaar: number;
    naam: string;
    beschrijving?: string;
    status: 'gepland' | 'bezig' | 'afgelopen' | 'geannuleerd';
    user_registered?: boolean;
    user_caliber?: string;
    rounds?: any[];
    created_at: string;
}

interface PageProps {
    competitions: Competition[];
}

const statusColors = {
    gepland: 'bg-blue-100 text-blue-800',
    bezig: 'bg-green-100 text-green-800',
    afgelopen: 'bg-gray-100 text-gray-800',
    geannuleerd: 'bg-red-100 text-red-800',
};

const statusLabels = {
    gepland: 'Gepland',
    bezig: 'Bezig',
    afgelopen: 'Afgelopen',
    geannuleerd: 'Geannuleerd',
};

export default function Competitions() {
    const { competitions } = usePage<PageProps>().props;
    const [registrationLoading, setRegistrationLoading] = useState<number | null>(null);

    const handleRegister = (competitionId: number, caliber: 'kkp' | 'gkp') => {
        setRegistrationLoading(competitionId);
        router.post(route('competitions.register', competitionId), {
            kaliber: caliber,
        }, {
            onFinish: () => setRegistrationLoading(null),
        });
    };

    const handleUnregister = (competitionId: number) => {
        setRegistrationLoading(competitionId);
        router.delete(route('competitions.unregister', competitionId), {
            onFinish: () => setRegistrationLoading(null),
        });
    };

    return (
        <Layout>
            <Head title="Competities" />
            
            <div className="max-w-6xl mx-auto px-4 py-8">
                <h1 className="text-3xl font-bold mb-8">Competities</h1>

                {competitions.length === 0 ? (
                    <div className="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                        <p className="text-gray-600">Geen competities beschikbaar op dit moment.</p>
                    </div>
                ) : (
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        {competitions.map((competition) => (
                            <div
                                key={competition.id}
                                className="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow"
                            >
                                <div className="p-6">
                                    <div className="flex justify-between items-start mb-3">
                                        <h2 className="text-xl font-bold text-gray-900">
                                            {competition.naam}
                                        </h2>
                                        <span
                                            className={`inline-block px-3 py-1 rounded-full text-xs font-semibold ${
                                                statusColors[competition.status]
                                            }`}
                                        >
                                            {statusLabels[competition.status]}
                                        </span>
                                    </div>

                                    <p className="text-gray-600 text-sm mb-4">
                                        Jaar: <strong>{competition.jaar}</strong>
                                    </p>

                                    {competition.beschrijving && (
                                        <p className="text-gray-700 text-sm mb-4 line-clamp-3">
                                            {competition.beschrijving}
                                        </p>
                                    )}

                                    <div className="mb-4 p-3 bg-gray-50 rounded text-sm">
                                        <p className="text-gray-700">
                                            <strong>Beurten:</strong> {competition.rounds?.length || 5} (van 5)
                                        </p>
                                    </div>

                                    <div className="flex gap-2">
                                        <Link
                                            href={route('competitions.show', competition.id)}
                                            className="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-center font-medium transition-colors"
                                        >
                                            Details
                                        </Link>

                                        {competition.user_registered ? (
                                            <button
                                                onClick={() => handleUnregister(competition.id)}
                                                disabled={registrationLoading === competition.id}
                                                className="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition-colors disabled:opacity-50"
                                            >
                                                {registrationLoading === competition.id
                                                    ? 'Laden...'
                                                    : 'Afmelden'}
                                            </button>
                                        ) : (
                                            <button
                                                onClick={() => handleRegister(competition.id, 'gkp')}
                                                disabled={
                                                    registrationLoading === competition.id ||
                                                    competition.status === 'afgelopen'
                                                }
                                                className="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition-colors disabled:opacity-50"
                                            >
                                                {registrationLoading === competition.id
                                                    ? 'Laden...'
                                                    : 'Deelnemen'}
                                            </button>
                                        )}
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </div>
        </Layout>
    );
}
