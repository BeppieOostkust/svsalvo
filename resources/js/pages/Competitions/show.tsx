import React, { useState } from "react";
import { Head, Link, usePage, router } from '@inertiajs/react';
import Layout from "@/components/Layout";
import { format, parseISO } from 'date-fns';
import { nl } from 'date-fns/locale';

interface CompetitionRound {
    id: number;
    round_number: number;
    naam: string;
    datum?: string;
    beschrijving?: string;
    scores: CompetitionScore[];
}

interface CompetitionScore {
    id: number;
    user: {
        id: number;
        name: string;
        avg_name?: string;
    };
    kaliber?: string;
    linker_score: number;
    rechter_score: number;
    totale_punten: number;
}

interface Competition {
    id: number;
    jaar: number;
    naam: string;
    beschrijving?: string;
    status: 'gepland' | 'bezig' | 'afgelopen' | 'geannuleerd';
}

interface CompetitionRegistration {
    id: number;
    user_id: number;
    kaliber: string;
    status: string;
}

interface PageProps {
    competition: Competition;
    rounds: CompetitionRound[];
    participants: any[];
    userRegistration?: CompetitionRegistration;
    [key: string]: any;
}

const calibersLabels: Record<string, string> = {
    meesterkaart_zwaar: 'Meesterkaart zwaar',
    meesterkaart_licht: 'Meesterkaart licht',
    kk_geweer_open_50m: 'KK geweer open richtmiddelen 50m',
    kk_geweer_optisch_100m: 'KK geweer optisch 100m',
    gk_precision_100m: 'Groot kaliber precisiegeweer target 100m',
    militair_geweer: 'Militair geweer',
    militair_geweer_optisch: 'Militair geweer optisch',
    veteranen_geweer: 'Veteranen geweer',
};

export default function CompetitionShow() {
    const { competition, rounds, userRegistration, totalParticipants = 0, visibleParticipants = 0, hiddenParticipants = 0, totalScores = 0, visibleScores = 0, hiddenScores = 0 } = usePage<PageProps>().props;
    const [selectedRound, setSelectedRound] = useState<number>(0);

    const [selectedCaliber, setSelectedCaliber] = useState<string>('');

    const currentRound = rounds[selectedRound];

    return (
        <Layout>
            <Head title={`Competitie ${competition.jaar}`} />

            <div className="max-w-6xl mx-auto px-4 py-8">
                {/* Header */}
                <div className="mb-8">
                    <h1 className="text-3xl font-bold mb-2">{competition.naam}</h1>
                    <p className="text-gray-600">
                        Jaar: <strong>{competition.jaar}</strong>
                    </p>
                    {competition.beschrijving && (
                        <p className="text-gray-700 mt-2">{competition.beschrijving}</p>
                    )}
                </div>

                {/* Rounds Navigation */}
                <div className="mb-8">
                    <h2 className="text-xl font-bold mb-4">Beurten</h2>
                    <div className="grid grid-cols-3 md:grid-cols-5 gap-2">
                        {rounds.map((round, index) => (
                            <button
                                key={round.id}
                                onClick={() => setSelectedRound(index)}
                                className={`px-4 py-2 rounded-lg font-medium transition-colors ${
                                    selectedRound === index
                                        ? 'bg-blue-600 text-white'
                                        : 'bg-gray-200 text-gray-800 hover:bg-gray-300'
                                }`}
                            >
                                {round.naam}
                            </button>
                        ))}
                    </div>
                </div>

                {/* Round Details */}
                {currentRound && (
                    <div className="mb-8">
                        <div className="bg-white rounded-lg shadow-lg p-6">
                            <h3 className="text-2xl font-bold mb-2">{currentRound.naam}</h3>
                            {currentRound.datum && (
                                <p className="text-gray-600 mb-4">
                                    Datum: <strong>{format(parseISO(currentRound.datum), 'd MMMM yyyy', { locale: nl })}</strong>
                                </p>
                            )}

                            {/* We intentionally hide/display discipline; show only points */}

                            {/* Leaderboard */}
                            <div className="overflow-x-auto">
                                <table className="w-full text-sm">
                                    <thead>
                                        <tr className="border-b-2 border-gray-300">
                                            <th className="px-3 py-2 text-left font-bold">Plaats</th>
                                            <th className="px-3 py-2 text-left font-bold">Schutter</th>
                                            <th className="px-3 py-2 text-center font-bold">Eerste Kaart</th>
                                            <th className="px-3 py-2 text-center font-bold">Tweede Kaart</th>
                                            <th className="px-3 py-2 text-right font-bold">Totaal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {currentRound.scores
                                            .slice() // copy
                                            .sort((a, b) => b.totale_punten - a.totale_punten)
                                            .map((score, index) => (
                                                <tr
                                                    key={score.id}
                                                    className={`border-b border-gray-200 ${
                                                        index % 2 === 0 ? 'bg-gray-50' : 'bg-white'
                                                    }`}
                                                >
                                                    <td className="px-3 py-3 font-bold text-center">
                                                        {index + 1}
                                                    </td>
                                                    <td className="px-3 py-3">
                                                        {score.user.avg_name || score.user.name}
                                                    </td>
                                                    <td className="px-3 py-3 text-center font-bold bg-blue-50">
                                                        {score.linker_score}
                                                    </td>
                                                    <td className="px-3 py-3 text-center font-bold bg-blue-50">
                                                        {score.rechter_score}
                                                    </td>
                                                    <td className="px-3 py-3 text-right font-bold text-lg bg-green-50">
                                                        {score.totale_punten}
                                                    </td>
                                                </tr>
                                            ))}
                                    </tbody>
                                </table>

                                {currentRound.scores.filter((s) => s.kaliber === selectedCaliber).length === 0 && (
                                    <div className="text-center py-8 text-gray-600">
                                        {totalScores > 0 && visibleScores === 0 ? (
                                            <>
                                                <p className="font-medium">Scores zijn aanwezig maar niet zichtbaar wegens privacy-instellingen.</p>
                                                <p className="text-sm mt-2">Sommige leden hebben gekozen hun scores privé te houden. Als je bent ingelogd kun je je eigen scores zien.</p>
                                            </>
                                        ) : (
                                            <p>
                                                Geen scores beschikbaar voor {calibersLabels[selectedCaliber] ?? selectedCaliber}.
                                            </p>
                                        )}
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>
                )}

                {/* Participant privacy note */}
                {hiddenParticipants > 0 && (
                    <div className="max-w-6xl mx-auto px-4">
                        <p className="text-sm text-gray-500">{hiddenParticipants} deelnemer(s) worden niet getoond vanwege privacy-instellingen.</p>
                    </div>
                )}

                {/* Back Button */}
                <div className="flex gap-2">
                    <Link
                        href={route('competitions.index')}
                        className="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 font-medium"
                    >
                        Terug naar Competities
                    </Link>
                </div>
            </div>
        </Layout>
    );
}
