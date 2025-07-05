import React from 'react';
import { Head, Link } from '@inertiajs/react';

export default function NotFound() {
    return (
        <>
            <Head title="404 - Pagina niet gevonden" />
            <div className="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center p-4">
                <div className="max-w-md w-full text-center">
                    {/* Animated 404 Number */}
                    <div className="mb-8">
                        <div className="relative">
                            <div className="text-8xl font-bold text-indigo-600 mb-4 animate-bounce">
                                404
                            </div>
                            {/* Floating elements */}
                            <div className="absolute top-0 left-0 w-full h-full pointer-events-none">
                                <div className="absolute top-4 left-8 w-3 h-3 bg-indigo-400 rounded-full animate-ping"></div>
                                <div className="absolute top-4 right-8 w-3 h-3 bg-indigo-400 rounded-full animate-ping" style={{ animationDelay: '0.5s' }}></div>
                                <div className="absolute bottom-4 left-12 w-2 h-2 bg-indigo-300 rounded-full animate-ping" style={{ animationDelay: '1s' }}></div>
                                <div className="absolute bottom-4 right-12 w-2 h-2 bg-indigo-300 rounded-full animate-ping" style={{ animationDelay: '1.5s' }}></div>
                            </div>
                            
                            {/* Confused face emoji */}
                            <div className="text-4xl animate-pulse mb-4">😕</div>
                        </div>
                    </div>

                    {/* Content with staggered animations */}
                    <div className="space-y-6">
                        <h1 className="text-4xl font-bold text-gray-800 opacity-0 animate-fadeIn404">
                            Oeps!
                        </h1>
                        <p className="text-xl text-gray-600 opacity-0 animate-fadeIn404" style={{ animationDelay: '0.2s' }}>
                            De pagina die je zoekt bestaat niet.
                        </p>
                        <p className="text-gray-500 opacity-0 animate-fadeIn404" style={{ animationDelay: '0.4s' }}>
                            Mogelijk is de link verouderd of heb je een typfout gemaakt.
                        </p>
                        
                        {/* Action buttons */}
                        <div className="flex flex-col sm:flex-row gap-4 justify-center opacity-0 animate-fadeIn404" style={{ animationDelay: '0.6s' }}>
                            <Link
                                href="/"
                                className="px-6 py-3 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition-all duration-200 transform hover:scale-105 hover:shadow-lg"
                            >
                                🏠 Terug naar Home
                            </Link>
                            <button
                                onClick={() => window.history.back()}
                                className="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-300 transition-all duration-200 transform hover:scale-105 hover:shadow-lg"
                            >
                                ⬅️ Ga Terug
                            </button>
                        </div>
                        
                        {/* Fun additional message */}
                        <div className="mt-8 opacity-0 animate-fadeIn404" style={{ animationDelay: '0.8s' }}>
                            <p className="text-sm text-gray-400">
                                Terwijl je hier bent, wist je dat onze website vol zit met coole features? 
                                Ga terug en ontdek ze! 🚀
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
