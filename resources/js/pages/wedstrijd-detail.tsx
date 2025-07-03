import React from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import Layout from '@/components/Layout';
import { format, parseISO } from 'date-fns';
import { nl } from 'date-fns/locale';

interface MatchGebruikerScore {
    id: number;
    gebruiker_id: number;
    wedstrijd_id: number;
    kaliber: string;
    totale_punten: number;
    linker_kaart_6: number;
    linker_kaart_7: number;
    linker_kaart_8: number;
    linker_kaart_9: number;
    linker_kaart_10: number;
    rechter_kaart_6: number;
    rechter_kaart_7: number;
    rechter_kaart_8: number;
    rechter_kaart_9: number;
    rechter_kaart_10: number;
    gebruiker?: {
        id: number;
        name: string;
    };
}

interface Match {
    id: number;
    naam: string;
    beschrijving: string;
    status: string;
    start_datum: string;
    created_at: string;
    updated_at: string;
    gebruikersScores: MatchGebruikerScore[];
}

interface PageProps {
    match: Match;
    auth?: {
        user?: {
            id: number;
            name: string;
        };
    };
    [key: string]: any;
}

export default function WedstrijdDetail() {
    const { props } = usePage<PageProps>();
    const { match } = props;

    const formatStatus = (status: string) => {
        switch (status) {
            case 'binnenkort': return 'Binnenkort';
            case 'bezig': return 'Bezig';
            case 'afgelopen': return 'Afgelopen';
            case 'geannuleerd': return 'Geannuleerd';
            default: return status;
        }
    };
    
    const getStatusColor = (status: string) => {
        switch (status) {
            case 'binnenkort': return 'bg-blue-100 text-blue-800';
            case 'bezig': return 'bg-green-100 text-green-800';
            case 'afgelopen': return 'bg-gray-100 text-gray-800';
            case 'geannuleerd': return 'bg-red-100 text-red-800';
            default: return 'bg-gray-100 text-gray-800';
        }
    };

    const formatDate = (dateString: string) => {
        try {
            const date = parseISO(dateString);
            return format(date, 'd MMMM yyyy HH:mm', { locale: nl });
        } catch (e) {
            return dateString;
        }
    };

    // Group scores by caliber - add null check
    const scoresByKaliber = (match.gebruikersScores || []).reduce((groups, score) => {
        const kaliber = score.kaliber;
        if (!groups[kaliber]) {
            groups[kaliber] = [];
        }
        groups[kaliber].push(score);
        return groups;
    }, {} as Record<string, MatchGebruikerScore[]>);

    // Sort scores within each caliber by total points (descending)
    Object.keys(scoresByKaliber).forEach(kaliber => {
        scoresByKaliber[kaliber].sort((a, b) => b.totale_punten - a.totale_punten);
    });

    return (
        <Layout>
            <Head title={`${match.naam} - Wedstrijddetails`} />
            
            <div className="w-[90%] mx-auto px-4 py-8">
                {/* Back navigation */}
                <div className="mb-6">
                    <Link 
                        href="/wedstrijden" 
                        className="text-blue-600 hover:text-blue-800 font-medium flex items-center text-sm"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fillRule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clipRule="evenodd" />
                        </svg>
                        Terug naar wedstrijden
                    </Link>
                </div>

                {/* Match Header */}
                <div className="bg-white shadow rounded-lg p-6 mb-6">
                    <div className="flex justify-between items-start mb-4">
                        <h1 className="text-3xl font-bold">{match.naam}</h1>
                        <span className={`inline-flex px-3 py-1 text-sm font-medium rounded-full ${getStatusColor(match.status)}`}>
                            {formatStatus(match.status)}
                        </span>
                    </div>
                    
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p className="text-gray-600 mb-2">
                                <span className="font-medium">Datum:</span> {formatDate(match.start_datum)}
                            </p>
                            {match.beschrijving && (
                                <div>
                                    <span className="font-medium text-gray-600">Beschrijving:</span>
                                    <p className="text-gray-700 mt-1">{match.beschrijving}</p>
                                </div>
                            )}
                        </div>
                        <div className="text-right">
                            <p className="text-sm text-gray-500">
                                Totaal aantal deelnemers: {match.gebruikersScores?.length || 0}
                            </p>
                            {(match.gebruikersScores?.length || 0) > 0 && (
                                <p className="text-xs text-gray-400 mt-1">
                                    * Alleen deelnemers die hun resultaten publiek hebben gemaakt worden getoond
                                </p>
                            )}
                        </div>
                    </div>
                </div>

                {/* Scores Section */}
                {(match.gebruikersScores?.length || 0) > 0 ? (
                    <div className="space-y-6">
                        {Object.keys(scoresByKaliber).map(kaliber => (
                            <div key={kaliber} className="bg-white shadow rounded-lg p-6">
                                <h2 className="text-xl font-semibold mb-4">
                                    {kaliber === 'gkp' ? 'Groot Kaliber Pistool (GKP)' : 
                                     kaliber === 'kkp' ? 'Klein Kaliber Pistool (KKP)' : 
                                     kaliber.toUpperCase()}
                                </h2>
                                
                                <div className="overflow-x-auto">
                                    <table className="min-w-full divide-y divide-gray-200">
                                        <thead className="bg-gray-50">
                                            <tr>
                                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Positie
                                                </th>
                                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Deelnemer
                                                </th>
                                                <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Totaal Punten
                                                </th>
                                                <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Linker Kaart
                                                </th>
                                                <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Rechter Kaart
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody className="bg-white divide-y divide-gray-200">
                                            {scoresByKaliber[kaliber].map((score, index) => {
                                                const linkerTotal = score.linker_kaart_6 + score.linker_kaart_7 + score.linker_kaart_8 + score.linker_kaart_9 + score.linker_kaart_10;
                                                const rechterTotal = score.rechter_kaart_6 + score.rechter_kaart_7 + score.rechter_kaart_8 + score.rechter_kaart_9 + score.rechter_kaart_10;
                                                
                                                return (
                                                    <tr key={score.id} className={index < 3 ? 'bg-yellow-50' : ''}>
                                                        <td className="px-6 py-4 whitespace-nowrap">
                                                            <div className="flex items-center">
                                                                {index === 0 && (
                                                                    <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mr-2">
                                                                        🥇 1e
                                                                    </span>
                                                                )}
                                                                {index === 1 && (
                                                                    <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 mr-2">
                                                                        🥈 2e
                                                                    </span>
                                                                )}
                                                                {index === 2 && (
                                                                    <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 mr-2">
                                                                        🥉 3e
                                                                    </span>
                                                                )}
                                                                {index > 2 && (
                                                                    <span className="text-gray-500 mr-2">
                                                                        {index + 1}e
                                                                    </span>
                                                                )}
                                                            </div>
                                                        </td>
                                                        <td className="px-6 py-4 whitespace-nowrap">
                                                            <div className="text-sm font-medium text-gray-900">
                                                                {score.gebruiker?.name || 'Onbekende deelnemer'}
                                                            </div>
                                                        </td>
                                                        <td className="px-6 py-4 whitespace-nowrap text-center">
                                                            <span className="text-lg font-semibold text-gray-900">
                                                                {score.totale_punten}
                                                            </span>
                                                        </td>
                                                        <td className="px-6 py-4 whitespace-nowrap text-center">
                                                            <div className="text-sm text-gray-900">
                                                                <div className="flex justify-center space-x-1">
                                                                    <span className="bg-gray-100 px-1 rounded">{score.linker_kaart_6}</span>
                                                                    <span className="bg-gray-100 px-1 rounded">{score.linker_kaart_7}</span>
                                                                    <span className="bg-gray-100 px-1 rounded">{score.linker_kaart_8}</span>
                                                                    <span className="bg-gray-100 px-1 rounded">{score.linker_kaart_9}</span>
                                                                    <span className="bg-gray-100 px-1 rounded">{score.linker_kaart_10}</span>
                                                                </div>
                                                                <div className="text-xs text-gray-500 mt-1">
                                                                    Totaal: {linkerTotal}
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td className="px-6 py-4 whitespace-nowrap text-center">
                                                            <div className="text-sm text-gray-900">
                                                                <div className="flex justify-center space-x-1">
                                                                    <span className="bg-gray-100 px-1 rounded">{score.rechter_kaart_6}</span>
                                                                    <span className="bg-gray-100 px-1 rounded">{score.rechter_kaart_7}</span>
                                                                    <span className="bg-gray-100 px-1 rounded">{score.rechter_kaart_8}</span>
                                                                    <span className="bg-gray-100 px-1 rounded">{score.rechter_kaart_9}</span>
                                                                    <span className="bg-gray-100 px-1 rounded">{score.rechter_kaart_10}</span>
                                                                </div>
                                                                <div className="text-xs text-gray-500 mt-1">
                                                                    Totaal: {rechterTotal}
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                );
                                            })}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        ))}
                    </div>
                ) : (
                    <div className="bg-white shadow rounded-lg p-8 text-center">
                        <div className="text-gray-500">
                            <svg className="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                            <h3 className="text-lg font-medium text-gray-900 mb-2">Nog geen resultaten</h3>
                            <p className="text-gray-500">
                                {match.status === 'binnenkort' ? 
                                    'Deze wedstrijd heeft nog niet plaatsgevonden.' :
                                    'Er zijn nog geen scores ingevoerd voor deze wedstrijd.'}
                            </p>
                        </div>
                    </div>
                )}
            </div>
        </Layout>
    );
}
