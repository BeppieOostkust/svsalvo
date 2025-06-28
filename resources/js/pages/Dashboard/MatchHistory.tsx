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
    matchScores: {
        data: MatchScore[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        next_page_url: string | null;
        prev_page_url: string | null;
    };
    [key: string]: any;
}

export default function MatchHistory() {
    const { matchScores } = usePage<PageProps>().props;

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

    return (
        <>
            <Head title="Mijn Scores" />
            <Header />
            
            <div className="w-[90%] mx-auto px-4 py-8">
                {/* Header */}
                <div className="mb-8">
                    <div className="flex items-center justify-between">
                        <div>
                            <h1 className="text-3xl font-bold text-gray-900 mb-2">Mijn Wedstrijdscores</h1>
                            <p className="text-gray-600">
                                Overzicht van al je wedstrijdresultaten ({matchScores.total} scores)
                            </p>
                        </div>
                        <Link
                            href="/dashboard"
                            className="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors"
                        >
                            ← Terug naar dashboard
                        </Link>
                    </div>
                </div>

                {/* Scores List */}
                {matchScores.data.length === 0 ? (
                    <div className="bg-white rounded-lg shadow-md p-8 text-center">
                        <p className="text-gray-500 text-lg">Je hebt nog geen wedstrijdscores.</p>
                        <Link
                            href="/wedstrijden"
                            className="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors"
                        >
                            Bekijk wedstrijden
                        </Link>
                    </div>
                ) : (
                    <div className="space-y-6">
                        {matchScores.data.map((score) => (
                            <div key={score.id} className="bg-white rounded-lg shadow-md p-6">
                                {/* Score Header */}
                                <div className="flex justify-between items-start mb-6">
                                    <div>
                                        <h2 className="text-xl font-semibold text-gray-900 mb-1">
                                            {score.matches.naam}
                                        </h2>
                                        <p className="text-gray-600 mb-2">
                                            {formatDateTime(score.matches.start_datum)}
                                        </p>
                                        {score.matches.beschrijving && (
                                            <p className="text-sm text-gray-500">
                                                {score.matches.beschrijving}
                                            </p>
                                        )}
                                    </div>
                                    <div className="text-right">
                                        <div className={`text-4xl font-bold ${getScoreColor(score.totale_punten)}`}>
                                            {score.totale_punten}
                                        </div>
                                        <span className={`px-3 py-1 text-sm rounded-full ${getDisciplineColor(score.kaliber)}`}>
                                            {score.kaliber.toUpperCase()}
                                        </span>
                                    </div>
                                </div>

                                {/* Detailed Score Breakdown */}
                                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    {/* Left Card */}
                                    <div className="bg-blue-50 rounded-lg p-4">
                                        <h3 className="font-semibold text-gray-800 mb-3 flex justify-between items-center">
                                            <span>Linker Kaart</span>
                                            <span className="text-sm text-gray-600">
                                                {calculateShotsCount(score, 'left')} schoten | {calculateLeftTotal(score)} punten
                                            </span>
                                        </h3>
                                        <div className="space-y-2">
                                            <div className="flex justify-between items-center">
                                                <span className="text-sm">6-ring:</span>
                                                <div className="flex items-center space-x-2">
                                                    <span className="font-mono bg-white px-2 py-1 rounded text-sm">
                                                        {score.linker_kaart_6}
                                                    </span>
                                                    <span className="text-sm text-gray-600">
                                                        = {score.linker_kaart_6 * 6}pt
                                                    </span>
                                                </div>
                                            </div>
                                            <div className="flex justify-between items-center">
                                                <span className="text-sm">7-ring:</span>
                                                <div className="flex items-center space-x-2">
                                                    <span className="font-mono bg-white px-2 py-1 rounded text-sm">
                                                        {score.linker_kaart_7}
                                                    </span>
                                                    <span className="text-sm text-gray-600">
                                                        = {score.linker_kaart_7 * 7}pt
                                                    </span>
                                                </div>
                                            </div>
                                            <div className="flex justify-between items-center">
                                                <span className="text-sm">8-ring:</span>
                                                <div className="flex items-center space-x-2">
                                                    <span className="font-mono bg-white px-2 py-1 rounded text-sm">
                                                        {score.linker_kaart_8}
                                                    </span>
                                                    <span className="text-sm text-gray-600">
                                                        = {score.linker_kaart_8 * 8}pt
                                                    </span>
                                                </div>
                                            </div>
                                            <div className="flex justify-between items-center">
                                                <span className="text-sm">9-ring:</span>
                                                <div className="flex items-center space-x-2">
                                                    <span className="font-mono bg-white px-2 py-1 rounded text-sm">
                                                        {score.linker_kaart_9}
                                                    </span>
                                                    <span className="text-sm text-gray-600">
                                                        = {score.linker_kaart_9 * 9}pt
                                                    </span>
                                                </div>
                                            </div>
                                            <div className="flex justify-between items-center">
                                                <span className="text-sm">10-ring:</span>
                                                <div className="flex items-center space-x-2">
                                                    <span className="font-mono bg-white px-2 py-1 rounded text-sm">
                                                        {score.linker_kaart_10}
                                                    </span>
                                                    <span className="text-sm text-gray-600">
                                                        = {score.linker_kaart_10 * 10}pt
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {/* Right Card */}
                                    <div className="bg-green-50 rounded-lg p-4">
                                        <h3 className="font-semibold text-gray-800 mb-3 flex justify-between items-center">
                                            <span>Rechter Kaart</span>
                                            <span className="text-sm text-gray-600">
                                                {calculateShotsCount(score, 'right')} schoten | {calculateRightTotal(score)} punten
                                            </span>
                                        </h3>
                                        <div className="space-y-2">
                                            <div className="flex justify-between items-center">
                                                <span className="text-sm">6-ring:</span>
                                                <div className="flex items-center space-x-2">
                                                    <span className="font-mono bg-white px-2 py-1 rounded text-sm">
                                                        {score.rechter_kaart_6}
                                                    </span>
                                                    <span className="text-sm text-gray-600">
                                                        = {score.rechter_kaart_6 * 6}pt
                                                    </span>
                                                </div>
                                            </div>
                                            <div className="flex justify-between items-center">
                                                <span className="text-sm">7-ring:</span>
                                                <div className="flex items-center space-x-2">
                                                    <span className="font-mono bg-white px-2 py-1 rounded text-sm">
                                                        {score.rechter_kaart_7}
                                                    </span>
                                                    <span className="text-sm text-gray-600">
                                                        = {score.rechter_kaart_7 * 7}pt
                                                    </span>
                                                </div>
                                            </div>
                                            <div className="flex justify-between items-center">
                                                <span className="text-sm">8-ring:</span>
                                                <div className="flex items-center space-x-2">
                                                    <span className="font-mono bg-white px-2 py-1 rounded text-sm">
                                                        {score.rechter_kaart_8}
                                                    </span>
                                                    <span className="text-sm text-gray-600">
                                                        = {score.rechter_kaart_8 * 8}pt
                                                    </span>
                                                </div>
                                            </div>
                                            <div className="flex justify-between items-center">
                                                <span className="text-sm">9-ring:</span>
                                                <div className="flex items-center space-x-2">
                                                    <span className="font-mono bg-white px-2 py-1 rounded text-sm">
                                                        {score.rechter_kaart_9}
                                                    </span>
                                                    <span className="text-sm text-gray-600">
                                                        = {score.rechter_kaart_9 * 9}pt
                                                    </span>
                                                </div>
                                            </div>
                                            <div className="flex justify-between items-center">
                                                <span className="text-sm">10-ring:</span>
                                                <div className="flex items-center space-x-2">
                                                    <span className="font-mono bg-white px-2 py-1 rounded text-sm">
                                                        {score.rechter_kaart_10}
                                                    </span>
                                                    <span className="text-sm text-gray-600">
                                                        = {score.rechter_kaart_10 * 10}pt
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {/* Penalties and Summary */}
                                {(score.aantal_schoten_buiten_tijd > 0 || score.afwaarderingen > 0) && (
                                    <div className="mt-6 bg-red-50 rounded-lg p-4">
                                        <h3 className="font-semibold text-red-800 mb-3">Aftrekpunten</h3>
                                        <div className="space-y-2">
                                            {score.aantal_schoten_buiten_tijd > 0 && (
                                                <div className="flex justify-between items-center">
                                                    <span className="text-sm text-red-700">
                                                        Schoten buiten tijd ({score.aantal_schoten_buiten_tijd}):
                                                    </span>
                                                    <span className="font-semibold text-red-800">
                                                        -{score.aantal_schoten_buiten_tijd * 2} punten
                                                    </span>
                                                </div>
                                            )}
                                            {score.afwaarderingen > 0 && (
                                                <div className="flex justify-between items-center">
                                                    <span className="text-sm text-red-700">
                                                        Afwaarderingen:
                                                    </span>
                                                    <span className="font-semibold text-red-800">
                                                        -{score.afwaarderingen} punten
                                                    </span>
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                )}

                                {/* Score Summary */}
                                <div className="mt-6 bg-gray-50 rounded-lg p-4">
                                    <div className="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                                        <div>
                                            <p className="text-sm text-gray-600">Totaal schoten</p>
                                            <p className="text-lg font-semibold">
                                                {calculateShotsCount(score, 'left') + calculateShotsCount(score, 'right')}
                                            </p>
                                        </div>
                                        <div>
                                            <p className="text-sm text-gray-600">Basis punten</p>
                                            <p className="text-lg font-semibold">
                                                {calculateLeftTotal(score) + calculateRightTotal(score)}
                                            </p>
                                        </div>
                                        <div>
                                            <p className="text-sm text-gray-600">Aftrek</p>
                                            <p className="text-lg font-semibold text-red-600">
                                                -{(score.aantal_schoten_buiten_tijd * 2) + score.afwaarderingen}
                                            </p>
                                        </div>
                                        <div>
                                            <p className="text-sm text-gray-600">Eindresultaat</p>
                                            <p className={`text-2xl font-bold ${getScoreColor(score.totale_punten)}`}>
                                                {score.totale_punten}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ))}

                        {/* Pagination */}
                        {matchScores.last_page > 1 && (
                            <div className="flex justify-center space-x-2 mt-8">
                                {matchScores.prev_page_url && (
                                    <Link
                                        href={matchScores.prev_page_url}
                                        className="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors"
                                    >
                                        Vorige
                                    </Link>
                                )}
                                
                                <span className="px-4 py-2 bg-blue-600 text-white rounded-lg">
                                    Pagina {matchScores.current_page} van {matchScores.last_page}
                                </span>
                                
                                {matchScores.next_page_url && (
                                    <Link
                                        href={matchScores.next_page_url}
                                        className="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors"
                                    >
                                        Volgende
                                    </Link>
                                )}
                            </div>
                        )}
                    </div>
                )}
            </div>
        </>
    );
}
