import React, { useState } from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import Header from '@/components/header';
import { format } from 'date-fns';
import { nl } from 'date-fns/locale';
import Layout from '@/components/Layout';

interface User {
    id: number;
    name: string;
    avg_name: string;
    email: string;
    first_name: string | null;
    last_name: string | null;
    date_of_birth: string | null;
    phone: string | null;
    address: string | null;
    city: string | null;
    postal_code: string | null;
    country: string;
    position: string | null;
    bio: string | null;
    profile_image: string | null;
    profile_image_url: string | null;
    member_since: string | null;
    preferred_discipline: string | null;
    license_number: string | null;
    license_expiry: string | null;
    is_admin: boolean;
    is_active_member: boolean;
    show_contact_info: boolean;
    show_scores_public: boolean;
}

interface CompetitionScore {
    id: number;
    kaliber: string;
    totale_punten: number;
    linker_score: number;
    rechter_score: number;
    created_at: string;
    round: {
        id: number;
        round_number: number;
        naam: string;
        datum: string | null;
        competition: {
            id: number;
            jaar: number;
            naam: string;
        };
    };
}

interface ActivityRegistration {
    id: number;
    status: string;
    registered_at: string;
    paid_amount: number;
    payment_confirmed: boolean;
    activity: {
        id: number;
        title: string;
        type: string;
        start_date: string;
        location: string;
    };
}

interface Stats {
    total_scores: number;
    best_score: number | null;
    average_score: number | null;
    recent_activities: ActivityRegistration[];
}

interface PageProps {
    user: User;
    competitionScores: CompetitionScore[];
    activityRegistrations: ActivityRegistration[];
    stats: Stats;
    [key: string]: any;
}

export default function UserDashboard() {
    const { user, competitionScores, activityRegistrations, stats } = usePage<PageProps>().props;
    
    // State for filtering and expanding match scores
    const [selectedCaliber, setSelectedCaliber] = useState<string>('all');
    const [expandedMatches, setExpandedMatches] = useState<Set<number>>(new Set());

    const formatDate = (dateString: string | null) => {
        if (!dateString) return 'Niet opgegeven';
        try {
            return format(new Date(dateString), 'd MMMM yyyy', { locale: nl });
        } catch {
            return dateString;
        }
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
            'meesterkaart_zwaar': 'bg-slate-100 text-slate-800',
            'meesterkaart_licht': 'bg-zinc-100 text-zinc-800',
            'kk_geweer_open_50m': 'bg-emerald-100 text-emerald-800',
            'kk_geweer_optisch_100m': 'bg-teal-100 text-teal-800',
            'gk_precision_100m': 'bg-sky-100 text-sky-800',
            'militair_geweer': 'bg-indigo-100 text-indigo-800',
            'militair_geweer_optisch': 'bg-violet-100 text-violet-800',
            'veteranen_geweer': 'bg-amber-100 text-amber-800',
        };
        return colors[discipline as keyof typeof colors] || 'bg-gray-100 text-gray-800';
    };

    const getCaliberLabel = (caliber: string) => {
        const labels: Record<string, string> = {
            meesterkaart_zwaar: 'Meesterkaart zwaar',
            meesterkaart_licht: 'Meesterkaart licht',
            kk_geweer_open_50m: 'KK geweer open richtmiddelen 50m',
            kk_geweer_optisch_100m: 'KK geweer optisch 100m',
            gk_precision_100m: 'Groot kaliber precisiegeweer target 100m',
            militair_geweer: 'Militair geweer',
            militair_geweer_optisch: 'Militair geweer optisch',
            veteranen_geweer: 'Veteranen geweer',
        };

        return labels[caliber] ?? caliber;
    };

    const getActivityTypeColor = (type: string) => {
        const colors = {
            'training': 'bg-blue-100 text-blue-800',
            'wedstrijd': 'bg-red-100 text-red-800',
            'evenement': 'bg-green-100 text-green-800',
            'vergadering': 'bg-gray-100 text-gray-800',
            'cursus': 'bg-purple-100 text-purple-800',
            'toernooi': 'bg-yellow-100 text-yellow-800'
        };
        return colors[type as keyof typeof colors] || 'bg-gray-100 text-gray-800';
    };

    // Filter match scores based on selected caliber
    const filteredMatchScores = selectedCaliber === 'all' 
        ? competitionScores 
        : competitionScores.filter(score => score.kaliber === selectedCaliber);

    // Get unique calibers for filter options
    const availableCalibers = [...new Set(competitionScores.map(score => score.kaliber))];

    // Toggle match expansion
    const toggleMatchExpansion = (matchId: number) => {
        const newExpanded = new Set(expandedMatches);
        if (newExpanded.has(matchId)) {
            newExpanded.delete(matchId);
        } else {
            newExpanded.add(matchId);
        }
        setExpandedMatches(newExpanded);
    };

    return (
        <Layout>
            <Head title="Dashboard" />
            
            
            <div className="w-[90%] mx-auto px-4 py-8">
                {/* Welcome Section */}
                <div className="mb-8">
                    <h1 className="text-3xl font-bold text-gray-900 mb-2">
                        Welkom, {user.first_name || user.name}!
                    </h1>
                    <p className="text-gray-600">
                        Hier vind je een overzicht van je persoonlijke informatie, scores en activiteiten.
                    </p>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {/* Left Column - Personal Info */}
                    <div className="lg:col-span-1 space-y-6">
                        {/* Profile Card */}
                        <div className="bg-white rounded-lg shadow-md p-6">
                            <div className="flex items-center space-x-4 mb-4">
                                {user.profile_image_url ? (
                                    <img
                                        src={user.profile_image_url}
                                        alt="Profile"
                                        className="w-16 h-16 rounded-full object-cover"
                                    />
                                ) : (
                                    <div className="w-16 h-16 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span className="text-gray-600 font-semibold text-xl">
                                            {(user.first_name?.[0] || user.name[0]).toUpperCase()}
                                        </span>
                                    </div>
                                )}
                                <div>
                                    <h2 className="text-xl font-semibold">
                                        {user.first_name && user.last_name 
                                            ? `${user.first_name} ${user.last_name}` 
                                            : user.name
                                        }
                                    </h2>
                                    <p className="text-gray-600">{user.email}</p>
                                    {user.position && (
                                        <span className="inline-block px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded mt-1">
                                            {user.position}
                                        </span>
                                    )}
                                </div>
                            </div>

                            <div className="space-y-3">
                                <div>
                                    <label className="text-sm font-medium text-gray-500">AVG Ledennaam</label>
                                    <p className="text-gray-900">{user.avg_name}</p>
                                </div>
                                
                                {user.date_of_birth && (
                                    <div>
                                        <label className="text-sm font-medium text-gray-500">Geboortedatum</label>
                                        <p className="text-gray-900">{formatDate(user.date_of_birth)}</p>
                                    </div>
                                )}
                                
                                {user.phone && (
                                    <div>
                                        <label className="text-sm font-medium text-gray-500">Telefoon</label>
                                        <p className="text-gray-900">{user.phone}</p>
                                    </div>
                                )}
                                
                                {user.address && (
                                    <div>
                                        <label className="text-sm font-medium text-gray-500">Adres</label>
                                        <p className="text-gray-900">
                                            {user.address}
                                            {user.city && <><br />{user.postal_code} {user.city}</>}
                                            {user.country && <><br />{user.country}</>}
                                        </p>
                                    </div>
                                )}
                                
                                {user.member_since && (
                                    <div>
                                        <label className="text-sm font-medium text-gray-500">Lid sinds</label>
                                        <p className="text-gray-900">{formatDate(user.member_since)}</p>
                                    </div>
                                )}
                                
                                {user.preferred_discipline && (
                                    <div>
                                        <label className="text-sm font-medium text-gray-500">Voorkeur discipline</label>
                                        <p className="text-gray-900 capitalize">{user.preferred_discipline}</p>
                                    </div>
                                )}
                                
                                {user.license_number && (
                                    <div>
                                        <label className="text-sm font-medium text-gray-500">Licentienummer</label>
                                        <p className="text-gray-900">{user.license_number}</p>
                                        {user.license_expiry && (
                                            <p className="text-sm text-gray-500">
                                                Verloopt: {formatDate(user.license_expiry)}
                                            </p>
                                        )}
                                    </div>
                                )}

                                {user.bio && (
                                    <div>
                                        <label className="text-sm font-medium text-gray-500">Biografie</label>
                                        <p className="text-gray-900 text-sm">{user.bio}</p>
                                    </div>
                                )}
                            </div>

                            <div className="mt-6 space-y-2">
                                <Link
                                    href="/profile"
                                    className="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-center block transition-colors"
                                >
                                    Profiel bewerken
                                </Link>
                                <Link
                                    href={route('wedstrijden.index')}
                                    className="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-center block transition-colors"
                                >
                                    📅 Bekijk wedstrijden
                                </Link>
                            </div>
                        </div>

                        {/* Statistics Card */}
                        <div className="bg-white rounded-lg shadow-md p-6">
                            <h3 className="text-lg font-semibold mb-4">Statistieken</h3>
                            <div className="space-y-4">
                                <div className="flex justify-between">
                                    <span className="text-gray-600">Totaal scores:</span>
                                    <span className="font-semibold">{stats.total_scores}</span>
                                </div>
                                <div className="flex justify-between">
                                    <span className="text-gray-600">Beste score:</span>
                                    <span className={`font-semibold ${stats.best_score ? getScoreColor(stats.best_score) : ''}`}>
                                        {stats.best_score || 'Nog geen scores'}
                                    </span>
                                </div>
                                <div className="flex justify-between">
                                    <span className="text-gray-600">Gemiddelde score:</span>
                                    <span className={`font-semibold ${stats.average_score ? getScoreColor(stats.average_score) : ''}`}>
                                        {stats.average_score ? Math.round(stats.average_score) : 'Nog geen scores'}
                                    </span>
                                </div>
                                <div className="pt-4 border-t border-gray-200">
                                    <Link
                                        href="/scores/openbaar"
                                        className="inline-flex items-center justify-center w-full px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors"
                                    >
                                        <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Bekijk Alle Openbare Scores
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Right Column - Scores and Activities */}
                    <div className="lg:col-span-2 space-y-8">
                        {/* Competition Scores Section */}
                        <div className="bg-white rounded-lg shadow-md p-6">
                            <div className="mb-6 flex items-center justify-between gap-4">
                                <h3 className="text-xl font-semibold">Mijn Wedstrijdscores</h3>
                                {competitionScores.length > 0 && (
                                    <Link
                                        href={route('competitions.index')}
                                        className="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors"
                                    >
                                        Alle Scores Bekijken
                                    </Link>
                                )}
                            </div>

                            {competitionScores.length === 0 ? (
                                <p className="text-gray-500 text-center py-8">
                                    Je hebt nog geen wedstrijdscores.
                                </p>
                            ) : (
                                <div>
                                    {competitionScores.length > 1 && (
                                        <div className="mb-6">
                                            <label className="block text-sm font-medium text-gray-700 mb-2">
                                                Filter op discipline:
                                            </label>
                                            <select
                                                value={selectedCaliber}
                                                onChange={(e) => setSelectedCaliber(e.target.value)}
                                                className="w-full md:w-96 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            >
                                                <option value="all">Alle disciplines</option>
                                                {availableCalibers.map((caliber) => (
                                                    <option key={caliber} value={caliber}>
                                                        {getCaliberLabel(caliber)}
                                                    </option>
                                                ))}
                                            </select>
                                        </div>
                                    )}

                                    <div className="space-y-4 max-h-[600px] overflow-y-auto">
                                        {filteredMatchScores.slice(0, 10).map((score) => (
                                            <div key={score.id} className="border border-gray-200 rounded-lg p-4">
                                                <div className="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                                    <div>
                                                        <h4 className="font-semibold text-gray-900">
                                                            {score.round.competition.naam} - {score.round.naam}
                                                        </h4>
                                                        <p className="text-sm text-gray-600">
                                                            {formatDateTime(score.created_at)}
                                                        </p>
                                                    </div>
                                                    <div className="text-right">
                                                        <span className={`text-2xl font-bold ${getScoreColor(score.totale_punten)}`}>
                                                            {score.totale_punten}
                                                        </span>
                                                        <div>
                                                            <span className={`inline-block mt-2 px-2 py-1 text-xs rounded ${getDisciplineColor(score.kaliber)}`}>
                                                                {getCaliberLabel(score.kaliber)}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div className="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                                                    <div className="rounded-md bg-blue-50 p-3">
                                                        <p className="text-gray-600">Linker score</p>
                                                        <p className="text-lg font-semibold text-gray-900">{score.linker_score}</p>
                                                    </div>
                                                    <div className="rounded-md bg-orange-50 p-3">
                                                        <p className="text-gray-600">Rechter score</p>
                                                        <p className="text-lg font-semibold text-gray-900">{score.rechter_score}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        ))}

                                        {filteredMatchScores.length === 0 && selectedCaliber !== 'all' && (
                                            <p className="text-gray-500 text-center py-8">
                                                Geen scores gevonden voor {getCaliberLabel(selectedCaliber)}.
                                            </p>
                                        )}
                                    </div>
                                </div>
                            )}
                        </div>

                        {/* Activity Registrations Section */}
                        <div className="bg-white rounded-lg shadow-md p-6">
                            <h3 className="text-xl font-semibold mb-6">Mijn Activiteiten</h3>
                            
                            {activityRegistrations.length === 0 ? (
                                <p className="text-gray-500 text-center py-8">
                                    Je bent nog niet aangemeld voor activiteiten.
                                </p>
                            ) : (
                                <div className="space-y-4 max-h-96 overflow-y-auto">
                                    {activityRegistrations.slice(0, 10).map((registration) => (
                                        <div key={registration.id} className="border border-gray-200 rounded-lg p-4">
                                            <div className="flex justify-between items-start">
                                                <div className="flex-1">
                                                    <h4 className="font-semibold text-gray-900">
                                                        {registration.activity.title}
                                                    </h4>
                                                    <p className="text-sm text-gray-600">
                                                        {formatDateTime(registration.activity.start_date)}
                                                    </p>
                                                    {registration.activity.location && (
                                                        <p className="text-sm text-gray-500">
                                                            📍 {registration.activity.location}
                                                        </p>
                                                    )}
                                                </div>
                                                <div className="flex flex-col items-end space-y-2">
                                                    <span className={`px-2 py-1 text-xs rounded ${getActivityTypeColor(registration.activity.type)}`}>
                                                        {registration.activity.type}
                                                    </span>
                                                    <span className={`px-2 py-1 text-xs rounded ${
                                                        registration.status === 'bevestigd' ? 'bg-green-100 text-green-800' :
                                                        registration.status === 'aangemeld' ? 'bg-blue-100 text-blue-800' :
                                                        registration.status === 'geannuleerd' ? 'bg-red-100 text-red-800' :
                                                        'bg-gray-100 text-gray-800'
                                                    }`}>
                                                        {registration.status}
                                                    </span>
                                                    {registration.paid_amount > 0 && (
                                                        <span className={`text-xs ${registration.payment_confirmed ? 'text-green-600' : 'text-orange-600'}`}>
                                                            €{registration.paid_amount} {registration.payment_confirmed ? '✓' : '⏳'}
                                                        </span>
                                                    )}
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
