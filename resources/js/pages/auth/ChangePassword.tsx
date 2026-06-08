import React, { useState, useEffect } from 'react';
import { Head, useForm } from '@inertiajs/react';
import Layout from '@/components/Layout';

interface Props {
    user: {
        first_name?: string;
        name: string;
        full_name: string;
    };
}

interface TypewriterTextProps {
    text: string;
    delay?: number;
    onComplete?: () => void;
}

function TypewriterText({ text, delay = 50, onComplete }: TypewriterTextProps) {
    const [displayedText, setDisplayedText] = useState('');
    const [currentIndex, setCurrentIndex] = useState(0);

    useEffect(() => {
        if (currentIndex < text.length) {
            const timeout = setTimeout(() => {
                setDisplayedText(prev => prev + text[currentIndex]);
                setCurrentIndex(prev => prev + 1);
            }, delay);

            return () => clearTimeout(timeout);
        } else if (onComplete) {
            onComplete();
        }
    }, [currentIndex, text, delay, onComplete]);

    return <span>{displayedText}</span>;
}

export default function ChangePassword({ user }: Props) {
    const [currentStep, setCurrentStep] = useState(0);
    const [showForm, setShowForm] = useState(false);
    
    const { data, setData, post, processing, errors } = useForm({
        new_password: '',
        new_password_confirmation: '',
    });

    const messages = [
        `Hoi, ${user.full_name}...`,
        `Welkom bij de vernieuwde site van SV Salvo!`,
        `Wij hebben u een tijdelijk wachtwoord gegeven om in te loggen.`,
        `Hier kunt u dat nu aanpassen voor uw veiligheid.`
    ];

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('password.change'));
    };

    const nextMessage = () => {
        if (currentStep < messages.length - 1) {
            setTimeout(() => setCurrentStep(prev => prev + 1), 1000);
        } else {
            setTimeout(() => setShowForm(true), 1500);
        }
    };

    return (
        <>
            <Head title="Wachtwoord wijzigen" />
            
            <div className="min-h-screen bg-white flex flex-col items-center justify-center px-4">
                <div className="w-full max-w-md">
                    {!showForm ? (
                        <div className="text-center space-y-8">
                            {/* Logo or Icon */}
                            <div className="mx-auto w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center mb-8">
                                <svg className="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 15v2m0 0v2m0-2h2m-2 0H10m4-10a4 4 0 00-8 0c0 2.21 1.79 4 4 4s4-1.79 4-4z" />
                                </svg>
                            </div>

                            {/* Typewriter Messages */}
                            <div className="space-y-6">
                                {messages.map((message, index) => (
                                    <div 
                                        key={index}
                                        className={`text-xl text-black font-medium min-h-[2rem] ${
                                            index <= currentStep ? 'opacity-100' : 'opacity-0'
                                        } transition-opacity duration-500`}
                                    >
                                        {index === currentStep && (
                                            <TypewriterText 
                                                text={message} 
                                                delay={30}
                                                onComplete={nextMessage}
                                            />
                                        )}
                                        {index < currentStep && message}
                                    </div>
                                ))}
                            </div>

                            {/* Loading animation */}
                            {currentStep === messages.length - 1 && !showForm && (
                                <div className="flex justify-center mt-8">
                                    <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                                </div>
                            )}
                        </div>
                    ) : (
                        <div className="bg-white p-8 rounded-lg border border-gray-200 animate-fadeIn">
                            <div className="text-center mb-8">
                                <h1 className="text-2xl font-bold text-black mb-2">
                                    Stel uw nieuwe wachtwoord in
                                </h1>
                                <p className="text-gray-600">
                                    Kies een nieuw, veilig wachtwoord voor uw account.
                                </p>
                            </div>

                            <form onSubmit={handleSubmit} className="space-y-6">
                                <div>
                                    <label htmlFor="new_password" className="block text-sm font-medium text-black mb-2">
                                        Nieuw wachtwoord
                                    </label>
                                    <input
                                        id="new_password"
                                        type="password"
                                        value={data.new_password}
                                        onChange={(e) => setData('new_password', e.target.value)}
                                        className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-black"
                                        required
                                        autoFocus
                                    />
                                    {errors.new_password && (
                                        <p className="mt-1 text-sm text-red-600">{errors.new_password}</p>
                                    )}
                                    <p className="mt-1 text-xs text-gray-500">
                                        Minimaal 8 karakters met letters en cijfers.
                                    </p>
                                </div>

                                <div>
                                    <label htmlFor="new_password_confirmation" className="block text-sm font-medium text-black mb-2">
                                        Bevestig nieuw wachtwoord
                                    </label>
                                    <input
                                        id="new_password_confirmation"
                                        type="password"
                                        value={data.new_password_confirmation}
                                        onChange={(e) => setData('new_password_confirmation', e.target.value)}
                                        className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-black"
                                        required
                                    />
                                </div>

                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="w-full bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white font-medium py-3 px-4 rounded-lg transition-colors"
                                >
                                    {processing ? (
                                        <span className="flex items-center justify-center">
                                            <svg className="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                                                <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Bezig met wijzigen...
                                        </span>
                                    ) : (
                                        'Wachtwoord wijzigen'
                                    )}
                                </button>
                            </form>

                            <div className="mt-6 text-center">
                                <p className="text-sm text-gray-500">
                                    Na het wijzigen van uw wachtwoord wordt u doorgeleid naar het dashboard.
                                </p>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </>
    );
}
