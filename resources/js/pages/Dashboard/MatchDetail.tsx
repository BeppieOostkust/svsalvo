import React from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import Header from '@/components/header';
import { format } from 'date-fns';
import { nl } from 'date-fns/locale';

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
    matchScore: MatchScore;
    [key: string]: any;
}

export default function MatchDetail() {
    const { matchScore } = usePage<PageProps>().props;

    const formatDateTime = (dateString: string) => {
        try {
            return format(new Date(dateString), 'd MMMM yyyy HH:mm', { locale: nl });
        } catch {
            return dateString;
        }
    };

    const getScoreColor = (score: number) => {
        if (score >= 220) return 'text-green-600 font-bold';
        if (score >= 180) return 'text-blue-600 font-semibold';
        if (score >= 140) return 'text-yellow-600';
        return 'text-red-600';
    };

    const getDisciplineColor = (discipline: string) => {
        const colors = {
            'gkp': 'bg-blue-100 text-blue-800',
            'kkp': 'bg-green-100 text-green-800',
            'lucht': 'bg-purple-100 text-purple-800'
        };
        return colors[discipline as keyof typeof colors] || 'bg-gray-100 text-gray-800';
    };

    const calculateLeftTotal = (score: MatchScore) => {
        return (score.linker_kaart_6 * 6) + (score.linker_kaart_7 * 7) + 
               (score.linker_kaart_8 * 8) + (score.linker_kaart_9 * 9) + 
               (score.linker_kaart_10 * 10);
    };

    const calculateRightTotal = (score: MatchScore) => {
        return (score.rechter_kaart_6 * 6) + (score.rechter_kaart_7 * 7) + 
               (score.rechter_kaart_8 * 8) + (score.rechter_kaart_9 * 9) + 
               (score.rechter_kaart_10 * 10);
    };

    const calculateShotsCount = (score: MatchScore, side: 'left' | 'right') => {
        if (side === 'left') {
            return score.linker_kaart_6 + score.linker_kaart_7 + score.linker_kaart_8 + 
                   score.linker_kaart_9 + score.linker_kaart_10;
        }
        return score.rechter_kaart_6 + score.rechter_kaart_7 + score.rechter_kaart_8 + 
               score.rechter_kaart_9 + score.rechter_kaart_10;
    };

    const leftTotal = calculateLeftTotal(matchScore);
    const rightTotal = calculateRightTotal(matchScore);
    const leftShots = calculateShotsCount(matchScore, 'left');
    const rightShots = calculateShotsCount(matchScore, 'right');

    return (
        <>
            <Head title={`Match Details - ${matchScore.matches.naam}`} />
            <Header />
            
            <div className="w-[90%] mx-auto px-4 py-8">
                {/* Navigation */}
                <div className="mb-6">
                    <Link
                        href="/dashboard"
                        className="text-blue-600 hover:text-blue-800 text-sm font-medium"
                    >
                        ← Terug naar Dashboard
                    </Link>
                </div>

                {/* Match Header */}
                <div className="bg-white rounded-lg shadow-md p-6 mb-8">
                    <div className="flex justify-between items-start">
                        <div>
                            <h1 className="text-3xl font-bold text-gray-900 mb-2">
                                {matchScore.matches.naam}
                            </h1>
                            <p className="text-gray-600 mb-4">
                                {formatDateTime(matchScore.matches.start_datum)}
                            </p>
                            {matchScore.matches.beschrijving && (
                                <p className="text-gray-700">
                                    {matchScore.matches.beschrijving}
                                </p>
                            )}
                        </div>
                        <div className="text-right">
                            <div className={`text-4xl font-bold ${getScoreColor(matchScore.totale_punten)} mb-2`}>
                                {matchScore.totale_punten}
                            </div>
                            <span className={`px-3 py-1 text-sm rounded-full ${getDisciplineColor(matchScore.kaliber)}`}>
                                {matchScore.kaliber.toUpperCase()}
                            </span>
                        </div>
                    </div>
                </div>

                {/* Detailed Score Breakdown */}
                <div className="bg-white rounded-lg shadow-md p-6">
                    <h2 className="text-2xl font-semibold text-gray-900 mb-6">Score Uitsplitsing</h2>
                    
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        {/* Left Card */}
                        <div className="bg-gray-50 rounded-lg p-6">
                            <h3 className="text-xl font-semibold text-gray-800 mb-4 text-center">
                                Linker Kaart
                            </h3>
                            
                            <div className="space-y-3">
                                <div className="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span className="font-medium">6-ring:</span>
                                    <div className="text-right">
                                        <span className="mr-2">{matchScore.linker_kaart_6} schoten</span>
                                        <span className="font-semibold">{matchScore.linker_kaart_6 * 6} punten</span>
                                    </div>
                                </div>
                                <div className="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span className="font-medium">7-ring:</span>
                                    <div className="text-right">
                                        <span className="mr-2">{matchScore.linker_kaart_7} schoten</span>
                                        <span className="font-semibold">{matchScore.linker_kaart_7 * 7} punten</span>
                                    </div>
                                </div>
                                <div className="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span className="font-medium">8-ring:</span>
                                    <div className="text-right">
                                        <span className="mr-2">{matchScore.linker_kaart_8} schoten</span>
                                        <span className="font-semibold">{matchScore.linker_kaart_8 * 8} punten</span>
                                    </div>
                                </div>
                                <div className="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span className="font-medium">9-ring:</span>
                                    <div className="text-right">
                                        <span className="mr-2">{matchScore.linker_kaart_9} schoten</span>
                                        <span className="font-semibold">{matchScore.linker_kaart_9 * 9} punten</span>
                                    </div>
                                </div>
                                <div className="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span className="font-medium">10-ring:</span>
                                    <div className="text-right">
                                        <span className="mr-2">{matchScore.linker_kaart_10} schoten</span>
                                        <span className="font-semibold">{matchScore.linker_kaart_10 * 10} punten</span>
                                    </div>
                                </div>
                                
                                <div className="bg-blue-100 rounded p-3 mt-4">
                                    <div className="flex justify-between items-center font-bold text-blue-800">
                                        <span>Totaal Links:</span>
                                        <div className="text-right">
                                            <span className="mr-2">{leftShots} schoten</span>
                                            <span className="text-lg">{leftTotal} punten</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Right Card */}
                        <div className="bg-gray-50 rounded-lg p-6">
                            <h3 className="text-xl font-semibold text-gray-800 mb-4 text-center">
                                Rechter Kaart
                            </h3>
                            
                            <div className="space-y-3">
                                <div className="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span className="font-medium">6-ring:</span>
                                    <div className="text-right">
                                        <span className="mr-2">{matchScore.rechter_kaart_6} schoten</span>
                                        <span className="font-semibold">{matchScore.rechter_kaart_6 * 6} punten</span>
                                    </div>
                                </div>
                                <div className="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span className="font-medium">7-ring:</span>
                                    <div className="text-right">
                                        <span className="mr-2">{matchScore.rechter_kaart_7} schoten</span>
                                        <span className="font-semibold">{matchScore.rechter_kaart_7 * 7} punten</span>
                                    </div>
                                </div>
                                <div className="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span className="font-medium">8-ring:</span>
                                    <div className="text-right">
                                        <span className="mr-2">{matchScore.rechter_kaart_8} schoten</span>
                                        <span className="font-semibold">{matchScore.rechter_kaart_8 * 8} punten</span>
                                    </div>
                                </div>
                                <div className="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span className="font-medium">9-ring:</span>
                                    <div className="text-right">
                                        <span className="mr-2">{matchScore.rechter_kaart_9} schoten</span>
                                        <span className="font-semibold">{matchScore.rechter_kaart_9 * 9} punten</span>
                                    </div>
                                </div>
                                <div className="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span className="font-medium">10-ring:</span>
                                    <div className="text-right">
                                        <span className="mr-2">{matchScore.rechter_kaart_10} schoten</span>
                                        <span className="font-semibold">{matchScore.rechter_kaart_10 * 10} punten</span>
                                    </div>
                                </div>
                                
                                <div className="bg-green-100 rounded p-3 mt-4">
                                    <div className="flex justify-between items-center font-bold text-green-800">
                                        <span>Totaal Rechts:</span>
                                        <div className="text-right">
                                            <span className="mr-2">{rightShots} schoten</span>
                                            <span className="text-lg">{rightTotal} punten</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Penalties Section */}
                    {(matchScore.aantal_schoten_buiten_tijd > 0 || matchScore.afwaarderingen > 0) && (
                        <div className="mt-8 bg-red-50 rounded-lg p-6">
                            <h3 className="text-xl font-semibold text-red-800 mb-4">Aftrekpunten</h3>
                            <div className="space-y-2">
                                {matchScore.aantal_schoten_buiten_tijd > 0 && (
                                    <div className="flex justify-between items-center">
                                        <span className="text-red-700">Schoten buiten tijd:</span>
                                        <div className="text-right text-red-800 font-semibold">
                                            <span className="mr-2">{matchScore.aantal_schoten_buiten_tijd} schoten</span>
                                            <span>-{matchScore.aantal_schoten_buiten_tijd * 2} punten</span>
                                        </div>
                                    </div>
                                )}
                                {matchScore.afwaarderingen > 0 && (
                                    <div className="flex justify-between items-center">
                                        <span className="text-red-700">Afwaarderingen:</span>
                                        <span className="text-red-800 font-semibold">-{matchScore.afwaarderingen} punten</span>
                                    </div>
                                )}
                            </div>
                        </div>
                    )}

                    {/* Final Total */}
                    <div className="mt-8 bg-gradient-to-r from-blue-50 to-green-50 rounded-lg p-6">
                        <div className="text-center">
                            <h3 className="text-2xl font-semibold text-gray-800 mb-2">Eindtotaal</h3>
                            <div className="text-6xl font-bold mb-2">
                                <span className="text-blue-600">{leftTotal}</span>
                                <span className="text-gray-400 mx-4">+</span>
                                <span className="text-green-600">{rightTotal}</span>
                                {(matchScore.aantal_schoten_buiten_tijd > 0 || matchScore.afwaarderingen > 0) && (
                                    <>
                                        <span className="text-gray-400 mx-4">-</span>
                                        <span className="text-red-600">
                                            {(matchScore.aantal_schoten_buiten_tijd * 2) + matchScore.afwaarderingen}
                                        </span>
                                    </>
                                )}
                            </div>
                            <div className={`text-5xl font-bold ${getScoreColor(matchScore.totale_punten)}`}>
                                = {matchScore.totale_punten} punten
                            </div>
                            <p className="text-gray-600 mt-2">
                                Totaal aantal schoten: {leftShots + rightShots}
                            </p>
                        </div>
                    </div>
                </div>

                {/* Action Buttons */}
                <div className="mt-8 flex gap-4 justify-center">
                    <Link
                        href="/my-scores"
                        className="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                    >
                        Alle Scores Bekijken
                    </Link>
                    <Link
                        href="/dashboard"
                        className="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
                    >
                        Terug naar Dashboard
                    </Link>
                </div>
            </div>
        </>
    );
}
