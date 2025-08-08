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
}

interface PageProps {
    users: User[];
}

export default function PublicScores({ users }: PageProps) {
    const [disciplineFilter, setDisciplineFilter] = useState<string>('all');

    const disciplines = {
        all: 'Alle disciplines',
        gkp: 'Grote Kaliber Pistool',
        kkp: 'Kleine Kaliber Pistool',
        gkg: 'Grote Kaliber Geweer',
        kkg: 'Kleine Kaliber Geweer',
        luchtpistool: 'Luchtpistool',
        luchtwapen: 'Luchtwapen',
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

    const filteredUsers = disciplineFilter === 'all'
        ? users
        : users.filter(user => user.preferred_discipline === disciplineFilter);

    const getInitials = (user: User) => {
        if (user.first_name && user.last_name) {
            return `${user.first_name[0]}${user.last_name[0]}`.toUpperCase();
        }
        return user.name.substring(0, 2).toUpperCase();
    };

    return (
        <Layout>
            <Head title="Openbare Scores" />
            

            <div className="w-[90%] mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div className="mb-8">
                    <h1 className="text-3xl font-bold text-gray-900">Openbare Scores</h1>
                    <p className="mt-2 text-gray-600">
                        Bekijk de scores van leden die hun resultaten openbaar delen.
                    </p>
                </div>

                {/* Filters and Actions */}
                <div className="mb-6 flex flex-col sm:flex-row gap-4 items-start sm:items-end">
                    <div className="flex-1">
                        <label htmlFor="discipline" className="block text-sm font-medium text-gray-700 mb-2">
                            Filter op discipline
                        </label>
                        <select
                            id="discipline"
                            value={disciplineFilter}
                            onChange={(e) => setDisciplineFilter(e.target.value)}
                            className="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                        >
                            {Object.entries(disciplines).map(([value, label]) => (
                                <option key={value} value={value}>
                                    {label}
                                </option>
                            ))}
                        </select>
                    </div>
                    <div>
                        <Link
                            href="/scores/leaderboard"
                            className="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors"
                        >
                            🏆 Leaderboard
                        </Link>
                    </div>
                </div>

                {/* Users Grid */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {filteredUsers.length === 0 ? (
                        <div className="col-span-full text-center py-12">
                            <p className="text-gray-500">
                                Geen leden gevonden die scores delen in deze discipline.
                            </p>
                        </div>
                    ) : (
                        filteredUsers.map((user) => (
                            <Card key={user.id} className="hover:shadow-lg transition-shadow">
                                <CardHeader className="flex flex-row items-center gap-4 p-6">
                                    <Avatar className="h-12 w-12">
                                        <AvatarImage src={user.profile_image || undefined} alt={user.name} />
                                        <AvatarFallback>{getInitials(user)}</AvatarFallback>
                                    </Avatar>
                                    <div>
                                        <h3 className="text-lg font-semibold text-gray-900">
                                            {user.first_name && user.last_name
                                                ? `${user.first_name} ${user.last_name}`
                                                : user.name}
                                        </h3>
                                        {user.preferred_discipline && (
                                            <Badge className={getDisciplineColor(user.preferred_discipline)}>
                                                {disciplines[user.preferred_discipline as keyof typeof disciplines]}
                                            </Badge>
                                        )}
                                    </div>
                                </CardHeader>
                                <CardContent className="p-6 pt-0">
                                    <Link
                                        href={`/scores/openbaar/${user.id}`}
                                        className="inline-flex items-center justify-center w-full px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                                    >
                                        Bekijk Scores
                                    </Link>
                                </CardContent>
                            </Card>
                        ))
                    )}
                </div>
            </div>
        </Layout>
    );
}
