import React from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import Header from '@/components/header';
import { format } from 'date-fns';
import { nl } from 'date-fns/locale';
import Layout from '@/components/Layout';

interface MatchScore {
    id: number;
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
    aantal_schoten_buiten_tijd: number;
    afwaarderingen: number;
    created_at: string;
    matches: {
        id: number;
        naam: string;
        beschrijving: string;
        status: string;
        start_datum: string;
    };
}

interface PageProps {
    matchScores: MatchScore[];
    match: {
        id: number;
        naam: string;
        beschrijving: string;
        status: string;
        start_datum: string;
    };
    [key: string]: any;
}

export default function MatchDetail() {
    const { matchScores, match } = usePage<PageProps>().props;

    // Guard clause - only show loading if matchScores is not available or empty
    if (!matchScores || matchScores.length === 0) {
        return (
            <Layout>
                <div className="w-[90%] max-w-4xl mx-auto px-4 py-8">
                    <div className="text-center">
                        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                        <p className="mt-4 text-gray-600">Gegevens laden...</p>
                    </div>
                </div>
            </Layout>
        );
    }

    // For now, let's take the first (latest) score
    const matchScore = matchScores[0];

    const formatDateTime = (dateString: string) => {
        try {
            return format(new Date(dateString), 'd MMMM yyyy HH:mm', { locale: nl });
        } catch {
            return dateString;
        }
    };

    const getScoreColor = (score: number) => {
        if (score >= 220) return 'text-green-600';
        if (score >= 180) return 'text-blue-600';
        if (score >= 140) return 'text-yellow-600';
        return 'text-red-600';
    };

    const getDisciplineColor = (discipline: string) => {
        const colors = {
            'gkp': 'bg-sky-100 text-sky-800',
            'kkp': 'bg-emerald-100 text-emerald-800',
            'gkg': 'bg-indigo-100 text-indigo-800',
            'kkg': 'bg-lime-100 text-lime-800',
            'luchtpistool': 'bg-pink-100 text-pink-800',
            'luchtwapen': 'bg-fuchsia-100 text-fuchsia-800',
        };
        return colors[discipline as keyof typeof colors] || 'bg-gray-100 text-gray-800';
    };

    // Calculate subtotals with safe defaults
    const linkerSubtotaal = 
        (matchScore.linker_kaart_6 || 0) * 6 +
        (matchScore.linker_kaart_7 || 0) * 7 +
        (matchScore.linker_kaart_8 || 0) * 8 +
        (matchScore.linker_kaart_9 || 0) * 9 +
        (matchScore.linker_kaart_10 || 0) * 10;

    const rechterSubtotaal = 
        (matchScore.rechter_kaart_6 || 0) * 6 +
        (matchScore.rechter_kaart_7 || 0) * 7 +
        (matchScore.rechter_kaart_8 || 0) * 8 +
        (matchScore.rechter_kaart_9 || 0) * 9 +
        (matchScore.rechter_kaart_10 || 0) * 10;

    const brutoTotaal = linkerSubtotaal + rechterSubtotaal;
    const totalePenalties = ((matchScore.aantal_schoten_buiten_tijd || 0) * 2) + (matchScore.afwaarderingen || 0);

    return (
        <Layout>
            <Head title={`Wedstrijd Details${match?.naam ? ` - ${match.naam}` : ''}`} />
            
            
            <div className="w-[90%] max-w-4xl mx-auto px-4 py-8">
                {/* Header Section */}
                <div className="mb-8">
                    <Link
                        href="/dashboard"
                        className="inline-flex items-center text-blue-600 hover:text-blue-800 mb-4"
                    >
                        <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                        </svg>
                        Terug naar Dashboard
                    </Link>
                    
                    <h1 className="text-3xl font-bold text-gray-900 mb-2">
                        {match?.naam || 'Wedstrijd Details'}
                    </h1>
                    {match?.start_datum && (
                        <p className="text-gray-600 mb-2">
                            {formatDateTime(match.start_datum)}
                        </p>
                    )}
                    {match?.beschrijving && (
                        <p className="text-gray-700">
                            {match.beschrijving}
                        </p>
                    )}
                </div>

                {/* Score Overview Card */}
                <div className="bg-white rounded-lg shadow-lg p-6 mb-8">
                    <div className="text-center mb-6">
                        <h2 className="text-2xl font-semibold mb-4">Mijn Score</h2>
                        <div className="flex items-center justify-center space-x-4">
                            <span className={`text-5xl font-bold ${getScoreColor(matchScore.totale_punten || 0)}`}>
                                {matchScore.totale_punten || 0}
                            </span>
                            <span className={`px-3 py-1 text-sm rounded-full ${getDisciplineColor(matchScore.kaliber || '')}`}>
                                {(matchScore.kaliber || '').toUpperCase()}
                            </span>
                        </div>
                        {matchScore.created_at && (
                            <p className="text-gray-600 mt-2">
                                Score ingevoerd op: {formatDateTime(matchScore.created_at)}
                            </p>
                        )}
                    </div>
                </div>

                {/* Detailed Score Breakdown */}
                <div className="bg-white rounded-lg shadow-lg p-6 mb-8">
                    <h3 className="text-xl font-semibold mb-6">Gedetailleerde Score Overzicht</h3>
                    
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        {/* Linker Kaart */}
                        <div className="bg-gray-50 rounded-lg p-4">
                            <h4 className="text-lg font-medium text-gray-900 mb-4 text-center">Linker Kaart</h4>
                            <div className="space-y-3">
                                <div className="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span className="font-medium">6-ring:</span>
                                    <div className="text-right">
                                        <span className="mr-2">{matchScore.linker_kaart_6 || 0} x 6 =</span>
                                        <span className="font-semibold">{(matchScore.linker_kaart_6 || 0) * 6}pt</span>
                                    </div>
                                </div>
                                <div className="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span className="font-medium">7-ring:</span>
                                    <div className="text-right">
                                        <span className="mr-2">{matchScore.linker_kaart_7 || 0} x 7 =</span>
                                        <span className="font-semibold">{(matchScore.linker_kaart_7 || 0) * 7}pt</span>
                                    </div>
                                </div>
                                <div className="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span className="font-medium">8-ring:</span>
                                    <div className="text-right">
                                        <span className="mr-2">{matchScore.linker_kaart_8 || 0} x 8 =</span>
                                        <span className="font-semibold">{(matchScore.linker_kaart_8 || 0) * 8}pt</span>
                                    </div>
                                </div>
                                <div className="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span className="font-medium">9-ring:</span>
                                    <div className="text-right">
                                        <span className="mr-2">{matchScore.linker_kaart_9 || 0} x 9 =</span>
                                        <span className="font-semibold">{(matchScore.linker_kaart_9 || 0) * 9}pt</span>
                                    </div>
                                </div>
                                <div className="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span className="font-medium">10-ring:</span>
                                    <div className="text-right">
                                        <span className="mr-2">{matchScore.linker_kaart_10 || 0} x 10 =</span>
                                        <span className="font-semibold">{(matchScore.linker_kaart_10 || 0) * 10}pt</span>
                                    </div>
                                </div>
                                <div className="flex justify-between items-center py-3 border-t-2 border-gray-300 bg-blue-50 rounded px-2">
                                    <span className="font-bold">Subtotaal Links:</span>
                                    <span className="font-bold text-blue-600">{linkerSubtotaal}pt</span>
                                </div>
                            </div>
                        </div>

                        {/* Rechter Kaart */}
                        <div className="bg-gray-50 rounded-lg p-4">
                            <h4 className="text-lg font-medium text-gray-900 mb-4 text-center">Rechter Kaart</h4>
                            <div className="space-y-3">
                                <div className="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span className="font-medium">6-ring:</span>
                                    <div className="text-right">
                                        <span className="mr-2">{matchScore.rechter_kaart_6 || 0} x 6 =</span>
                                        <span className="font-semibold">{(matchScore.rechter_kaart_6 || 0) * 6}pt</span>
                                    </div>
                                </div>
                                <div className="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span className="font-medium">7-ring:</span>
                                    <div className="text-right">
                                        <span className="mr-2">{matchScore.rechter_kaart_7 || 0} x 7 =</span>
                                        <span className="font-semibold">{(matchScore.rechter_kaart_7 || 0) * 7}pt</span>
                                    </div>
                                </div>
                                <div className="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span className="font-medium">8-ring:</span>
                                    <div className="text-right">
                                        <span className="mr-2">{matchScore.rechter_kaart_8 || 0} x 8 =</span>
                                        <span className="font-semibold">{(matchScore.rechter_kaart_8 || 0) * 8}pt</span>
                                    </div>
                                </div>
                                <div className="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span className="font-medium">9-ring:</span>
                                    <div className="text-right">
                                        <span className="mr-2">{matchScore.rechter_kaart_9 || 0} x 9 =</span>
                                        <span className="font-semibold">{(matchScore.rechter_kaart_9 || 0) * 9}pt</span>
                                    </div>
                                </div>
                                <div className="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span className="font-medium">10-ring:</span>
                                    <div className="text-right">
                                        <span className="mr-2">{matchScore.rechter_kaart_10 || 0} x 10 =</span>
                                        <span className="font-semibold">{(matchScore.rechter_kaart_10 || 0) * 10}pt</span>
                                    </div>
                                </div>
                                <div className="flex justify-between items-center py-3 border-t-2 border-gray-300 bg-blue-50 rounded px-2">
                                    <span className="font-bold">Subtotaal Rechts:</span>
                                    <span className="font-bold text-blue-600">{rechterSubtotaal}pt</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Score Calculation Summary */}
                <div className="bg-white rounded-lg shadow-lg p-6 mb-8">
                    <h3 className="text-xl font-semibold mb-6">Score Berekening</h3>
                    
                    <div className="space-y-4">
                        <div className="flex justify-between items-center py-3 border-b border-gray-200">
                            <span className="text-lg">Linker kaart totaal:</span>
                            <span className="text-lg font-semibold">{linkerSubtotaal}pt</span>
                        </div>
                        <div className="flex justify-between items-center py-3 border-b border-gray-200">
                            <span className="text-lg">Rechter kaart totaal:</span>
                            <span className="text-lg font-semibold">{rechterSubtotaal}pt</span>
                        </div>
                        <div className="flex justify-between items-center py-3 border-b border-gray-200">
                            <span className="text-lg font-medium">Bruto totaal:</span>
                            <span className="text-lg font-semibold">{brutoTotaal}pt</span>
                        </div>
                        
                        {/* Penalties */}
                        {totalePenalties > 0 && (
                            <>
                                {(matchScore.aantal_schoten_buiten_tijd || 0) > 0 && (
                                    <div className="flex justify-between items-center py-2 text-red-600">
                                        <span>Schoten buiten tijd ({matchScore.aantal_schoten_buiten_tijd || 0} x -2):</span>
                                        <span className="font-semibold">-{(matchScore.aantal_schoten_buiten_tijd || 0) * 2}pt</span>
                                    </div>
                                )}
                                {(matchScore.afwaarderingen || 0) > 0 && (
                                    <div className="flex justify-between items-center py-2 text-red-600">
                                        <span>Afwaarderingen:</span>
                                        <span className="font-semibold">-{matchScore.afwaarderingen || 0}pt</span>
                                    </div>
                                )}
                                <div className="flex justify-between items-center py-2 border-b border-gray-200 text-red-600">
                                    <span className="font-medium">Totale aftrek:</span>
                                    <span className="font-semibold">-{totalePenalties}pt</span>
                                </div>
                            </>
                        )}
                        
                        <div className="flex justify-between items-center py-4 border-t-2 border-gray-300 bg-gray-50 rounded px-4">
                            <span className="text-xl font-bold">Eindtotaal:</span>
                            <span className={`text-2xl font-bold ${getScoreColor(matchScore.totale_punten || 0)}`}>
                                {matchScore.totale_punten || 0}pt
                            </span>
                        </div>
                    </div>
                </div>

                {/* Action Buttons */}
                <div className="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link
                        href="/dashboard"
                        className="inline-flex items-center justify-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors"
                    >
                        <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 5a2 2 0 012-2h4a2 2 0 012 2v3H8V5z" />
                        </svg>
                        Terug naar Dashboard
                    </Link>
                    
                    <Link
                        href="/my-scores"
                        className="inline-flex items-center justify-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors"
                    >
                        <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Alle Mijn Scores
                    </Link>
                    
                </div>
            </div>
        </Layout>
    );
}
