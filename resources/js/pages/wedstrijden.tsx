import React, { useState } from "react";
import { Head, Link, usePage, router } from '@inertiajs/react';
import Layout from "@/components/Layout";

// Registration Modal Component
interface RegistrationModalProps {
    isOpen: boolean;
    onClose: () => void;
    onSubmit: (caliber: string) => void;
    competitionName: string;
    loading: boolean;
}

function RegistrationModal({ isOpen, onClose, onSubmit, competitionName, loading }: RegistrationModalProps) {
    const [selectedCaliber, setSelectedCaliber] = useState<string>('gkp');

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        onSubmit(selectedCaliber);
    };

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div className="bg-white rounded-lg p-6 w-full max-w-md">
                <h3 className="text-lg font-semibold mb-4">Aanmelden voor {competitionName}</h3>
                
                <form onSubmit={handleSubmit} className="space-y-4">
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-2">
                            Selecteer kaliber *
                        </label>
                        <div className="space-y-2">
                            <label className="flex items-center">
                                <input
                                    type="radio"
                                    value="gkp"
                                    checked={selectedCaliber === 'gkp'}
                                    onChange={(e) => setSelectedCaliber(e.target.value)}
                                    className="mr-2"
                                />
                                Groot Kaliber Pistool (GKP)
                            </label>
                            <label className="flex items-center">
                                <input
                                    type="radio"
                                    value="kkp"
                                    checked={selectedCaliber === 'kkp'}
                                    onChange={(e) => setSelectedCaliber(e.target.value)}
                                    className="mr-2"
                                />
                                Klein Kaliber Pistool (KKP)
                            </label>
                        </div>
                    </div>

                    <div className="flex gap-3 pt-4">
                        <button
                            type="button"
                            onClick={onClose}
                            disabled={loading}
                            className="flex-1 px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 disabled:opacity-50"
                        >
                            Annuleren
                        </button>
                        <button
                            type="submit"
                            disabled={loading}
                            className="flex-1 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:bg-green-400"
                        >
                            {loading ? 'Bezig...' : 'Aanmelden'}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}

interface Round {
    id: number;
    competition_id: number;
    round_number: number;
    naam: string;
}

interface Competition {
    id: number;
    naam: string;
    beschrijving: string;
    status: string;
    jaar: number;
    created_at: string;
    updated_at: string;
    rounds?: Round[];
    is_user_registered?: boolean;
    user_caliber?: string;
    user_registration_id?: number;
}

interface PageProps {
    matches: Competition[];
    latestCompetition?: Competition | null;
    leaderboard?: any[];
    auth?: {
        user?: {
            id: number;
            name: string;
        };
    };
    [key: string]: any;
}

export default function Wedstrijden() {
    const { props } = usePage<PageProps>();
    const competitions = props?.matches || [];
    const latestCompetition = props?.latestCompetition;
    const leaderboard = props?.leaderboard || [];
    const currentUser = props?.auth?.user;
    const [loading, setLoading] = useState<number | null>(null);
    const [registrationModal, setRegistrationModal] = useState<{
        isOpen: boolean;
        competition: Competition | null;
    }>({ isOpen: false, competition: null });

    console.log("Page Props:", props);
    console.log("Competitions data:", competitions);

    const getStatusColor = (status: string) => {
        switch (status) {
            case 'actief': return 'bg-green-100 text-green-800';
            case 'concept': return 'bg-blue-100 text-blue-800';
            case 'gesloten': return 'bg-gray-100 text-gray-800';
            default: return 'bg-gray-100 text-gray-800';
        }
    };

    const formatStatus = (status: string) => {
        switch (status) {
            case 'actief': return 'Actief';
            case 'concept': return 'Concept';
            case 'gesloten': return 'Gesloten';
            default: return status;
        }
    };

    const isUserRegistered = (competition: Competition) => {
        return competition.is_user_registered || false;
    };

    const canRegister = (competition: Competition) => {
        return currentUser && !isUserRegistered(competition);
    };

    const canUnregister = (competition: Competition) => {
        return currentUser && isUserRegistered(competition);
    };

    const handleRegistration = async (competitionId: number, action: 'register' | 'unregister') => {
        if (!currentUser) {
            router.visit('/login');
            return;
        }

        if (action === 'register') {
            // Open registration modal for caliber selection
            const competition = competitions.find(c => c.id === competitionId);
            if (competition) {
                setRegistrationModal({ isOpen: true, competition });
            }
            return;
        }

        // Handle unregistration directly
        setLoading(competitionId);
        
        try {
            router.delete(`/wedstrijd/${competitionId}/afmelden`, {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    // Manually reload the page data to refresh registration status
                    router.reload({ only: ['matches'] });
                },
                onError: (errors) => {
                    console.error('Unregistration failed:', errors);
                },
                onFinish: () => {
                    setLoading(null);
                }
            });
        } catch (error) {
            console.error('Error during unregistration:', error);
            setLoading(null);
        }
    };

    const closeRegistrationModal = () => {
        setRegistrationModal({ isOpen: false, competition: null });
    };

    const handleRegistrationSubmit = (caliber: string) => {
        if (!registrationModal.competition) return;

        setLoading(registrationModal.competition.id);
        
        router.post(`/wedstrijd/${registrationModal.competition.id}/aanmelden`, {
            calibers: [caliber]
        }, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                setRegistrationModal({ isOpen: false, competition: null });
                // Manually reload the page data to refresh registration status
                router.reload({ only: ['matches'] });
            },
            onError: (errors) => {
                console.error('Registration failed:', errors);
                console.error('Full error object:', JSON.stringify(errors, null, 2));
                // Show a user-friendly error message
                if (errors.message) {
                    alert('Aanmelding mislukt: ' + errors.message);
                } else {
                    alert('Aanmelding mislukt. Probeer het opnieuw.');
                }
            },
            onFinish: () => {
                setLoading(null);
            }
        });
    };

    return (
        <Layout>
            <Head title="Competities" />
            <div className="w-[90%] mx-auto px-4 py-8">
                <div className="flex justify-between items-center mb-6">
                    <h1 className="text-3xl font-bold">Competities</h1>
                    {!currentUser && (
                        <Link 
                            href="/login" 
                            className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium"
                        >
                            Inloggen om aan te melden
                        </Link>
                    )}
                </div>

                {/* Leaderboard Section */}
                {latestCompetition && leaderboard.length > 0 && (
                    <div className="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-lg shadow-lg mb-8 p-6 border-l-4 border-yellow-400">
                        <div className="flex items-center gap-2 mb-4">
                            <span className="text-2xl">🏆</span>
                            <h2 className="text-2xl font-bold text-gray-900">Leaderboard - {latestCompetition.naam}</h2>
                        </div>
                        <div className="overflow-x-auto">
                            <table className="w-full text-sm">
                                <thead className="border-b-2 border-yellow-300">
                                    <tr className="text-left">
                                        <th className="px-4 py-2 font-semibold text-gray-700">Positie</th>
                                        <th className="px-4 py-2 font-semibold text-gray-700">Schutter</th>
                                        <th className="px-4 py-2 font-semibold text-gray-700">Kaliber</th>
                                        <th className="px-4 py-2 font-semibold text-gray-700 text-right">Totaal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {leaderboard.map((entry: any, index: number) => (
                                        <tr key={`${entry.user_id}_${entry.kaliber}_${index}`} className="border-b hover:bg-yellow-100 transition-colors">
                                            <td className="px-4 py-3 font-bold text-lg text-yellow-600 w-12">
                                                {index === 0 ? '🥇' : index === 1 ? '🥈' : index === 2 ? '🥉' : `${index + 1}.`}
                                            </td>
                                            <td className="px-4 py-3 font-medium text-gray-900">
                                                {entry.user_name}
                                            </td>
                                            <td className="px-4 py-3 text-gray-700">
                                                <span className={`inline-block px-2 py-1 rounded text-xs font-semibold ${entry.kaliber === 'gkp' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'}`}>
                                                    {entry.kaliber === 'gkp' ? 'GKP' : 'KKP'}
                                                </span>
                                            </td>
                                            <td className="px-4 py-3 font-bold text-right text-gray-900">
                                                {entry.total_points.toLocaleString()}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                        <p className="text-sm text-gray-600 mt-4 text-center">
                            Gebaseerd op scores van {latestCompetition.rounds?.length || 0} rondes
                        </p>
                    </div>
                )}
                
                {competitions.length === 0 ? (
                    <div className="bg-white shadow rounded-lg p-8 text-center">
                        <p className="text-gray-500">Geen competities gevonden.</p>
                    </div>
                ) : (
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        {competitions.map((competition: Competition) => (
                            <div key={competition.id} className="bg-white shadow rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                                <div className="p-6">
                                    <div className="flex justify-between items-start mb-4">
                                        <h2 className="text-xl font-semibold">{competition.naam}</h2>
                                        <span className={`inline-flex px-2 py-1 text-xs font-medium rounded-full ${getStatusColor(competition.status)}`}>
                                            {formatStatus(competition.status)}
                                        </span>
                                    </div>
                                    
                                    <p className="text-gray-500 mb-4">
                                        <span className="font-medium">Jaar:</span> {competition.jaar}
                                    </p>
                                    
                                    {competition.beschrijving && (
                                        <p className="text-gray-700 mb-4">{competition.beschrijving}</p>
                                    )}

                                    {competition.rounds && (
                                        <p className="text-gray-600 mb-4">
                                            <span className="font-medium">Rondes:</span> {competition.rounds.length}
                                        </p>
                                    )}
                                
                                    
                                    {/* Action buttons */}
                                    <div className="flex gap-2 mb-4">
                                        {canRegister(competition) && (
                                            <button
                                                onClick={() => setRegistrationModal({ isOpen: true, competition })}
                                                disabled={loading === competition.id}
                                                className="flex-1 bg-green-600 hover:bg-green-700 disabled:bg-green-400 text-white px-4 py-2 rounded font-medium text-sm transition-colors"
                                            >
                                                {loading === competition.id ? 'Bezig...' : 'Aanmelden'}
                                            </button>
                                        )}
                                        
                                        {canUnregister(competition) && (
                                            <button
                                                onClick={() => handleRegistration(competition.id, 'unregister')}
                                                disabled={loading === competition.id}
                                                className="flex-1 bg-orange-600 hover:bg-orange-700 disabled:bg-orange-400 text-white px-4 py-2 rounded font-medium text-sm transition-colors"
                                            >
                                                {loading === competition.id ? 'Bezig...' : 'Afmelden'}
                                            </button>
                                        )}
                                        
                                        {isUserRegistered(competition) && (
                                            <div className="flex-1 bg-blue-100 text-blue-700 px-4 py-2 rounded font-medium text-sm text-center">
                                                Aangemeld: {competition.user_caliber?.toUpperCase()}
                                            </div>
                                        )}
                                    </div>
                                    
                                    <div className="flex justify-between items-center">
                                        <Link 
                                            href={`/wedstrijd/${competition.id}`} 
                                            className="text-blue-600 hover:text-blue-800 font-medium flex items-center text-sm"
                                        >
                                            Bekijk details
                                            <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
                                                <path fillRule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clipRule="evenodd" />
                                            </svg>
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </div>

            {/* Registration Modal */}
            <RegistrationModal
                isOpen={registrationModal.isOpen}
                onClose={closeRegistrationModal}
                onSubmit={handleRegistrationSubmit}
                competitionName={registrationModal.competition?.naam || ''}
                loading={loading === registrationModal.competition?.id}
            />
        </Layout>
    );
}