import React, { useState } from "react";
import { Head, Link, usePage, router } from '@inertiajs/react';
import Layout from "@/components/Layout";
import { format, parseISO } from 'date-fns';
import { nl } from 'date-fns/locale';

// Registration Modal Component
interface RegistrationModalProps {
    isOpen: boolean;
    onClose: () => void;
    onSubmit: (caliber: string, notes?: string) => void;
    matchName: string;
    loading: boolean;
}

function RegistrationModal({ isOpen, onClose, onSubmit, matchName, loading }: RegistrationModalProps) {
    const [caliber, setCaliber] = useState<string>('gkp');
    const [notes, setNotes] = useState<string>('');

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        onSubmit(caliber, notes);
    };

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div className="bg-white rounded-lg p-6 w-full max-w-md">
                <h3 className="text-lg font-semibold mb-4">Aanmelden voor {matchName}</h3>
                
                <form onSubmit={handleSubmit} className="space-y-4">
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-2">
                            Kaliber *
                        </label>
                        <div className="space-y-2">
                            <label className="flex items-center">
                                <input
                                    type="radio"
                                    name="caliber"
                                    value="gkp"
                                    checked={caliber === 'gkp'}
                                    onChange={(e) => setCaliber(e.target.value)}
                                    className="mr-2"
                                />
                                Groot Kaliber Pistool (GKP)
                            </label>
                            <label className="flex items-center">
                                <input
                                    type="radio"
                                    name="caliber"
                                    value="kkp"
                                    checked={caliber === 'kkp'}
                                    onChange={(e) => setCaliber(e.target.value)}
                                    className="mr-2"
                                />
                                Klein Kaliber Pistool (KKP)
                            </label>
                        </div>
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-2">
                            Opmerkingen (optioneel)
                        </label>
                        <textarea
                            value={notes}
                            onChange={(e) => setNotes(e.target.value)}
                            className="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"
                            rows={3}
                            maxLength={500}
                            placeholder="Bijv. speciale verzoeken of opmerkingen..."
                        />
                        <p className="text-xs text-gray-500 mt-1">{notes.length}/500 tekens</p>
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

interface MatchRegistration {
    id: number;
    match_id: number;
    user_id: number;
    caliber: string;
    status: string;
    registered_at: string;
    notes?: string;
    converted_to_participant: boolean;
    user?: {
        id: number;
        name: string;
        show_in_participants: boolean;
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
    registrations?: MatchRegistration[];
    is_user_registered?: boolean;
    is_participant?: boolean;
    has_registration?: boolean;
}

interface PageProps {
    matches: Match[];
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
    const matches = props?.matches || [];
    const currentUser = props?.auth?.user;
    const [loading, setLoading] = useState<number | null>(null);
    const [registrationModal, setRegistrationModal] = useState<{
        isOpen: boolean;
        match: Match | null;
    }>({ isOpen: false, match: null });

    console.log("Page Props:", props);
    console.log("Matches data:", matches);

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

    const isUserRegistered = (match: Match) => {
        if (!currentUser) return false;
        return match.gebruikersScores?.some(score => score.gebruiker_id === currentUser.id) || false;
    };

    const canRegister = (match: Match) => {
        return match.status === 'binnenkort' && currentUser && !isUserRegistered(match);
    };

    const canUnregister = (match: Match) => {
        return match.status === 'binnenkort' && currentUser && isUserRegistered(match);
    };

    const handleRegistration = async (matchId: number, action: 'register' | 'unregister') => {
        if (!currentUser) {
            router.visit('/login');
            return;
        }

        if (action === 'register') {
            // Open registration modal for caliber selection
            const match = matches.find(m => m.id === matchId);
            if (match) {
                setRegistrationModal({ isOpen: true, match });
            }
            return;
        }

        // Handle unregistration directly
        setLoading(matchId);
        
        try {
            router.delete(`/wedstrijd/${matchId}/afmelden`, {
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

    const openRegistrationModal = (match: Match) => {
        setRegistrationModal({ isOpen: true, match });
    };

    const closeRegistrationModal = () => {
        setRegistrationModal({ isOpen: false, match: null });
    };

    const handleRegistrationSubmit = (caliber: string, notes?: string) => {
        if (!registrationModal.match) return;

        setLoading(registrationModal.match.id);
        
        router.post(`/wedstrijd/${registrationModal.match.id}/aanmelden`, {
            caliber,
            notes
        }, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                setRegistrationModal({ isOpen: false, match: null });
                // Manually reload the page data to refresh registration status
                router.reload({ only: ['matches'] });
            },
            onError: (errors) => {
                console.error('Registration failed:', errors);
            },
            onFinish: () => {
                setLoading(null);
            }
        });
    };

    return (
        <Layout>
            <Head title="Wedstrijden" />
            <div className="w-[90%] mx-auto px-4 py-8">
                <div className="flex justify-between items-center mb-6">
                    <h1 className="text-3xl font-bold">Wedstrijden</h1>
                    {!currentUser && (
                        <Link 
                            href="/login" 
                            className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium"
                        >
                            Inloggen om aan te melden
                        </Link>
                    )}
                </div>
                
                {matches.length === 0 ? (
                    <div className="bg-white shadow rounded-lg p-8 text-center">
                        <p className="text-gray-500">Geen wedstrijden gevonden.</p>
                    </div>
                ) : (
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        {matches.map((match: Match) => (
                            <div key={match.id} className="bg-white shadow rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                                <div className="p-6">
                                    <div className="flex justify-between items-start mb-4">
                                        <h2 className="text-xl font-semibold">{match.naam}</h2>
                                        <span className={`inline-flex px-2 py-1 text-xs font-medium rounded-full ${getStatusColor(match.status)}`}>
                                            {formatStatus(match.status)}
                                        </span>
                                    </div>
                                    
                                    <p className="text-gray-500 mb-4">
                                        <span className="font-medium">Datum:</span> {formatDate(match.start_datum)}
                                    </p>
                                    
                                    {match.beschrijving && (
                                        <p className="text-gray-700 mb-4">{match.beschrijving}</p>
                                    )}
                                
                                    
                                    {/* Action buttons */}
                                    <div className="flex gap-2 mb-4">
                                        {canRegister(match) && !match.is_user_registered && (
                                            <button
                                                onClick={() => openRegistrationModal(match)}
                                                disabled={loading === match.id}
                                                className="flex-1 bg-green-600 hover:bg-green-700 disabled:bg-green-400 text-white px-4 py-2 rounded font-medium text-sm transition-colors"
                                            >
                                                {loading === match.id ? 'Bezig...' : 'Aanmelden'}
                                            </button>
                                        )}
                                        
                                        {match.has_registration && !match.is_participant && match.status === 'binnenkort' && (
                                            <button
                                                onClick={() => handleRegistration(match.id, 'unregister')}
                                                disabled={loading === match.id}
                                                className="flex-1 bg-orange-600 hover:bg-orange-700 disabled:bg-orange-400 text-white px-4 py-2 rounded font-medium text-sm transition-colors"
                                            >
                                                {loading === match.id ? 'Bezig...' : 'Aanmelding intrekken'}
                                            </button>
                                        )}
                                        
                                        {match.is_participant && (
                                            <div className="flex-1 bg-blue-100 text-blue-700 px-4 py-2 rounded font-medium text-sm text-center">
                                                Je bent deelnemer
                                            </div>
                                        )}
                                        
                                        {match.status === 'afgelopen' && (
                                            <div className="flex-1 bg-gray-100 text-gray-500 px-4 py-2 rounded font-medium text-sm text-center">
                                                Wedstrijd afgelopen
                                            </div>
                                        )}

                                        {match.status === 'geannuleerd' && (
                                            <div className="flex-1 bg-red-100 text-red-500 px-4 py-2 rounded font-medium text-sm text-center">
                                                Wedstrijd geannuleerd
                                            </div>
                                        )}
                                    </div>
                                    
                                    <div className="flex justify-between items-center">
                                        <Link 
                                            href={`/wedstrijd/${match.id}`} 
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
                matchName={registrationModal.match?.naam || ''}
                loading={loading === registrationModal.match?.id}
            />
        </Layout>
    );
}