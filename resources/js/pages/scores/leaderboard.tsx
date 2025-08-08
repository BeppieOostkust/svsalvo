import React, { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import Layout from '@/components/Layout';

interface User {
    id: number;
    name: string;
    first_name: string | null;
    last_name: string | null;
    profile_image: string | null;
    preferred_discipline: string | null;
    total_score?: number;
    average_score?: number;
    match_count?: number;
}

interface PageProps {
    leaderboard: User[];
}

export default function Leaderboard({ leaderboard }: PageProps) {
    const [disciplineFilter, setDisciplineFilter] = useState<string>('gkp');
    const [scoreType, setScoreType] = useState<string>('total');

    const disciplines = {
        gkp: 'Grote Kaliber Pistool (GKP)',
        kkp: 'Kleine Kaliber Pistool (KKP)',
        gkg: 'Grote Kaliber Geweer (GKG)',
        kkg: 'Kleine Kaliber Geweer (KKG)',
        luchtpistool: 'Luchtpistool',
        luchtwapen: 'Luchtwapen',
    };

    const scoreTypes = {
        total: 'Totale Scores',
        average: 'Gemiddelde Scores',
    };

    const getDisciplineColor = (discipline: string | null) => {
        if (!discipline) return 'bg-gray-100 text-gray-800';
        
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

    const getInitials = (user: User) => {
        if (user.first_name && user.last_name) {
            return `${user.first_name[0]}${user.last_name[0]}`.toUpperCase();
        }
        return user.name.substring(0, 2).toUpperCase();
    };

    const getRankIcon = (position: number) => {
        switch (position) {
            case 1:
                return '🥇';
            case 2:
                return '🥈';
            case 3:
                return '🥉';
            default:
                return `#${position}`;
        }
    };

    // Filter users based on selected discipline and score type
    const filteredUsers = leaderboard
        .filter(user => user.preferred_discipline === disciplineFilter)
        .sort((a, b) => {
            const scoreA = scoreType === 'total' ? (a.total_score || 0) : (a.average_score || 0);
            const scoreB = scoreType === 'total' ? (b.total_score || 0) : (b.average_score || 0);
            return scoreB - scoreA;
        });

    return (
        <Layout>
            <Head title="Leaderboard" />

            <div className="w-[90%] mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div className="mb-8">
                    <div className="flex items-center justify-between">
                        <div>
                            <h1 className="text-3xl font-bold text-gray-900">🏆 Leaderboard</h1>
                            <p className="mt-2 text-gray-600">
                                Bekijk de ranglijst van de beste schutters per discipline.
                            </p>
                        </div>
                        <Link
                            href="/scores/openbaar"
                            className="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                        >
                            ← Terug naar Scores
                        </Link>
                    </div>
                </div>

                {/* Filters */}
                <div className="mb-8 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label htmlFor="discipline" className="block text-sm font-medium text-gray-700 mb-2">
                            Discipline
                        </label>
                        <select
                            id="discipline"
                            value={disciplineFilter}
                            onChange={(e) => setDisciplineFilter(e.target.value)}
                            className="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                        >
                            {Object.entries(disciplines).map(([value, label]) => (
                                <option key={value} value={value}>
                                    {label}
                                </option>
                            ))}
                        </select>
                    </div>
                    <div>
                        <label htmlFor="scoreType" className="block text-sm font-medium text-gray-700 mb-2">
                            Score Type
                        </label>
                        <select
                            id="scoreType"
                            value={scoreType}
                            onChange={(e) => setScoreType(e.target.value)}
                            className="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                        >
                            {Object.entries(scoreTypes).map(([value, label]) => (
                                <option key={value} value={value}>
                                    {label}
                                </option>
                            ))}
                        </select>
                    </div>
                </div>

                {/* Current Selection Info */}
                <div className="mb-6 p-4 bg-blue-50 rounded-lg">
                    <h3 className="text-lg font-semibold text-blue-900 mb-2">
                        {disciplines[disciplineFilter as keyof typeof disciplines]} - {scoreTypes[scoreType as keyof typeof scoreTypes]}
                    </h3>
                    <p className="text-blue-700 text-sm">
                        {scoreType === 'total' 
                            ? 'Ranglijst gebaseerd op de totale scores van alle wedstrijden.'
                            : 'Ranglijst gebaseerd op de gemiddelde scores per wedstrijd.'
                        }
                    </p>
                </div>

                {/* Leaderboard */}
                <div className="space-y-4">
                    {filteredUsers.length === 0 ? (
                        <div className="text-center py-12">
                            <p className="text-gray-500">
                                Geen scores gevonden voor {disciplines[disciplineFilter as keyof typeof disciplines]}.
                            </p>
                        </div>
                    ) : (
                        filteredUsers.map((user, index) => {
                            const position = index + 1;
                            const score = scoreType === 'total' ? user.total_score : user.average_score;
                            const isTopThree = position <= 3;

                            return (
                                <Card 
                                    key={user.id} 
                                    className={`transition-all duration-200 ${isTopThree ? 'ring-2 ring-yellow-400 shadow-lg' : 'hover:shadow-md'}`}
                                >
                                    <CardContent className="p-6">
                                        <div className="flex items-center gap-6">
                                            {/* Position */}
                                            <div className={`text-center min-w-[4rem] ${isTopThree ? 'text-2xl' : 'text-xl'}`}>
                                                <div className={`font-bold ${isTopThree ? 'text-yellow-600' : 'text-gray-600'}`}>
                                                    {getRankIcon(position)}
                                                </div>
                                            </div>

                                            {/* Avatar */}
                                            <Avatar className={`${isTopThree ? 'h-16 w-16' : 'h-12 w-12'}`}>
                                                <AvatarImage src={user.profile_image || undefined} alt={user.name} />
                                                <AvatarFallback>{getInitials(user)}</AvatarFallback>
                                            </Avatar>

                                            {/* User Info */}
                                            <div className="flex-1">
                                                <h3 className={`font-semibold text-gray-900 ${isTopThree ? 'text-xl' : 'text-lg'}`}>
                                                    {user.first_name && user.last_name
                                                        ? `${user.first_name} ${user.last_name}`
                                                        : user.name}
                                                </h3>
                                                <div className="flex items-center gap-2 mt-1">
                                                    <Badge className={getDisciplineColor(user.preferred_discipline)}>
                                                        {disciplines[user.preferred_discipline as keyof typeof disciplines]}
                                                    </Badge>
                                                    {user.match_count && (
                                                        <span className="text-sm text-gray-500">
                                                            {user.match_count} wedstrijd{user.match_count !== 1 ? 'en' : ''}
                                                        </span>
                                                    )}
                                                </div>
                                            </div>

                                            {/* Score */}
                                            <div className="text-right">
                                                <div className={`font-bold ${isTopThree ? 'text-2xl text-yellow-600' : 'text-xl text-gray-900'}`}>
                                                    {score?.toFixed(scoreType === 'average' ? 1 : 0) || '0'}
                                                </div>
                                                <div className="text-sm text-gray-500">
                                                    {scoreType === 'total' ? 'totaal' : 'gemiddeld'}
                                                </div>
                                            </div>

                                            {/* View Details */}
                                            <div>
                                                <Link
                                                    href={`/scores/openbaar/${user.id}`}
                                                    className="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                                                >
                                                    Details
                                                </Link>
                                            </div>
                                        </div>
                                    </CardContent>
                                </Card>
                            );
                        })
                    )}
                </div>
            </div>
        </Layout>
    );
}
