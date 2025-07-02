import React, { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import Header from '@/components/header';
import { format } from 'date-fns';
import { nl } from 'date-fns/locale';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';

interface User {
    id: number;
    name: string;
    first_name: string | null;
    last_name: string | null;
    profile_image: string | null;
    preferred_discipline: string | null;
}

interface Match {
    id: number;
    start_datum: string;
    naam: string;
}

interface Score {
    id: number;
    matches: Match;
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
}

interface PageProps {
    user: User;
    scores: Score[];
}

export default function UserScores({ user, scores }: PageProps) {
    const [selectedCaliber, setSelectedCaliber] = useState<string>('all');
    const [expandedMatches, setExpandedMatches] = useState<Set<number>>(new Set());

    const disciplines = {
        all: 'Alle disciplines',
        gkp: 'Grote Kaliber Pistool',
        kkp: 'Kleine Kaliber Pistool',
        gkg: 'Grote Kaliber Geweer',
        kkg: 'Kleine Kaliber Geweer',
        luchtpistool: 'Luchtpistool',
        luchtwapen: 'Luchtwapen',
    };

    const getInitials = (user: User) => {
        if (user.first_name && user.last_name) {
            return `${user.first_name[0]}${user.last_name[0]}`.toUpperCase();
        }
        return user.name.substring(0, 2).toUpperCase();
    };

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
            'gkp': 'bg-sky-100 text-sky-800',
            'kkp': 'bg-emerald-100 text-emerald-800',
            'gkg': 'bg-indigo-100 text-indigo-800',
            'kkg': 'bg-lime-100 text-lime-800',
            'luchtpistool': 'bg-pink-100 text-pink-800',
            'luchtwapen': 'bg-fuchsia-100 text-fuchsia-800',
        };
        return colors[discipline as keyof typeof colors] || 'bg-gray-100 text-gray-800';
    };

    const toggleMatchExpansion = (matchId: number) => {
        const newExpanded = new Set(expandedMatches);
        if (newExpanded.has(matchId)) {
            newExpanded.delete(matchId);
        } else {
            newExpanded.add(matchId);
        }
        setExpandedMatches(newExpanded);
    };

    const filteredScores = selectedCaliber === 'all'
        ? scores
        : scores.filter(score => score.kaliber === selectedCaliber);

    // Calculate statistics
    const stats = {
        total_matches: scores.length,
        best_score: scores.length > 0 ? Math.max(...scores.map(s => s.totale_punten)) : null,
        average_score: scores.length > 0 
            ? Math.round(scores.reduce((acc, s) => acc + s.totale_punten, 0) / scores.length)
            : null
    };

    return (
        <Layout>
            <Head title={`Scores van ${user.first_name || user.name}`} />
            

            <div className="w-[90%] mx-auto px-4 sm:px-6 lg:px-8 py-12">
                {/* User Header */}
                <div className="mb-8">
                    <div className="flex items-center gap-4">
                        <Avatar className="h-16 w-16">
                            <AvatarImage src={user.profile_image || undefined} alt={user.name} />
                            <AvatarFallback>{getInitials(user)}</AvatarFallback>
                        </Avatar>
                        <div>
                            <h1 className="text-3xl font-bold text-gray-900">
                                {user.first_name && user.last_name
                                    ? `${user.first_name} ${user.last_name}`
                                    : user.name}
                            </h1>
                            {user.preferred_discipline && (
                                <Badge className={getDisciplineColor(user.preferred_discipline)}>
                                    {disciplines[user.preferred_discipline as keyof typeof disciplines]}
                                </Badge>
                            )}
                        </div>
                    </div>
                </div>

                {/* Statistics Card */}
                <Card className="mb-8">
                    <CardHeader>
                        <h2 className="text-xl font-semibold">Statistieken</h2>
                    </CardHeader>
                    <CardContent>
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <p className="text-sm text-gray-500">Totaal wedstrijden</p>
                                <p className="text-2xl font-semibold">{stats.total_matches}</p>
                            </div>
                            <div>
                                <p className="text-sm text-gray-500">Beste score</p>
                                <p className={`text-2xl font-semibold ${stats.best_score ? getScoreColor(stats.best_score) : ''}`}>
                                    {stats.best_score || 'N/A'}
                                </p>
                            </div>
                            <div>
                                <p className="text-sm text-gray-500">Gemiddelde score</p>
                                <p className={`text-2xl font-semibold ${stats.average_score ? getScoreColor(stats.average_score) : ''}`}>
                                    {stats.average_score || 'N/A'}
                                </p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Scores Section */}
                <div className="bg-white rounded-lg shadow-md p-6">
                    <div className="mb-6">
                        <h2 className="text-xl font-semibold mb-4">Wedstrijdscores</h2>
                        
                        {/* Caliber Filter */}
                        <select
                            value={selectedCaliber}
                            onChange={(e) => setSelectedCaliber(e.target.value)}
                            className="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                        >
                            {Object.entries(disciplines).map(([value, label]) => (
                                <option key={value} value={value}>
                                    {label}
                                </option>
                            ))}
                        </select>
                    </div>

                    {filteredScores.length === 0 ? (
                        <p className="text-gray-500 text-center py-8">
                            Geen scores gevonden{selectedCaliber !== 'all' ? ` voor ${disciplines[selectedCaliber as keyof typeof disciplines]}` : ''}.
                        </p>
                    ) : (
                        <div className="space-y-4">
                            {filteredScores.map((score) => (
                                <div key={score.id} className="border border-gray-200 rounded-lg">
                                    <div 
                                        className="p-4 cursor-pointer hover:bg-gray-50"
                                        onClick={() => toggleMatchExpansion(score.id)}
                                    >
                                        <div className="flex justify-between items-start">
                                            <div>
                                                <h3 className="font-medium text-gray-900">
                                                    {score.matches.naam}
                                                    <span className="text-xs ml-1">
                                                        {expandedMatches.has(score.id) ? 'Inklapppen' : 'Uitklappen'}
                                                    </span>
                                                </h3>
                                                <p className="text-sm text-gray-600">
                                                    {formatDateTime(score.matches.start_datum)}
                                                </p>
                                            </div>
                                            <div className="text-right">
                                                <span className={`text-2xl font-bold ${getScoreColor(score.totale_punten)}`}>
                                                    {score.totale_punten}
                                                </span>
                                                <span className={`ml-2 px-2 py-1 text-xs rounded ${getDisciplineColor(score.kaliber)}`}>
                                                    {score.kaliber.toUpperCase()}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    {expandedMatches.has(score.id) && (
                                        <div className="px-4 pb-4">
                                            <div className="bg-gray-50 rounded p-3">
                                                <div className="grid grid-cols-2 gap-4 text-sm">
                                                    <div>
                                                        <p className="font-medium text-gray-700 mb-2">Links</p>
                                                        <div className="space-y-1">
                                                            <div className="flex justify-between">
                                                                <span>6-ring: {score.linker_kaart_6}</span>
                                                                <span>{score.linker_kaart_6 * 6}pt</span>
                                                            </div>
                                                            <div className="flex justify-between">
                                                                <span>7-ring: {score.linker_kaart_7}</span>
                                                                <span>{score.linker_kaart_7 * 7}pt</span>
                                                            </div>
                                                            <div className="flex justify-between">
                                                                <span>8-ring: {score.linker_kaart_8}</span>
                                                                <span>{score.linker_kaart_8 * 8}pt</span>
                                                            </div>
                                                            <div className="flex justify-between">
                                                                <span>9-ring: {score.linker_kaart_9}</span>
                                                                <span>{score.linker_kaart_9 * 9}pt</span>
                                                            </div>
                                                            <div className="flex justify-between">
                                                                <span>10-ring: {score.linker_kaart_10}</span>
                                                                <span>{score.linker_kaart_10 * 10}pt</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <p className="font-medium text-gray-700 mb-2">Rechts</p>
                                                        <div className="space-y-1">
                                                            <div className="flex justify-between">
                                                                <span>6-ring: {score.rechter_kaart_6}</span>
                                                                <span>{score.rechter_kaart_6 * 6}pt</span>
                                                            </div>
                                                            <div className="flex justify-between">
                                                                <span>7-ring: {score.rechter_kaart_7}</span>
                                                                <span>{score.rechter_kaart_7 * 7}pt</span>
                                                            </div>
                                                            <div className="flex justify-between">
                                                                <span>8-ring: {score.rechter_kaart_8}</span>
                                                                <span>{score.rechter_kaart_8 * 8}pt</span>
                                                            </div>
                                                            <div className="flex justify-between">
                                                                <span>9-ring: {score.rechter_kaart_9}</span>
                                                                <span>{score.rechter_kaart_9 * 9}pt</span>
                                                            </div>
                                                            <div className="flex justify-between">
                                                                <span>10-ring: {score.rechter_kaart_10}</span>
                                                                <span>{score.rechter_kaart_10 * 10}pt</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    )}
                                </div>
                            ))}
                        </div>
                    )}
                </div>

                {/* Back Button */}
                <div className="mt-8">
                    <Link
                        href="/scores/openbaar"
                        className="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors"
                    >
                        <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Terug naar Overzicht
                    </Link>
                </div>
            </div>
        </Layout>
    );
}
