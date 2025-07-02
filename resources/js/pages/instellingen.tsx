import React from 'react';
import { Head } from '@inertiajs/react';
import Header from '@/components/header';
import Layout from '@/components/Layout';

export default function Instellingen() {
    return (
        <Layout>
            <Head title="Instellingen" />
            
            
            <div className="w-[90%] mx-auto px-4 py-8">
                {/* Header */}
                <div className="mb-8">
                    <h1 className="text-4xl font-bold text-gray-900 mb-4">Instellingen</h1>
                    <p className="text-xl text-gray-600">
                        Reglementen, huisregels en belangrijke informatie voor leden.
                    </p>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    {/* Club Rules */}
                    <div className="bg-white rounded-lg shadow-md p-6">
                        <h2 className="text-2xl font-bold text-gray-900 mb-6">Clubreglementen</h2>
                        
                        <div className="space-y-6">
                            <div>
                                <h3 className="text-lg font-semibold text-gray-900 mb-3">Algemene Regels</h3>
                                <ul className="list-disc list-inside space-y-2 text-gray-700">
                                    <li>Alle leden dienen zich te houden aan de veiligheidsregels</li>
                                    <li>Wapens mogen alleen onder begeleiding gebruikt worden</li>
                                    <li>Alcohol en drugs zijn ten strengste verboden</li>
                                    <li>Respect voor andere leden en trainers is verplicht</li>
                                    <li>Schade aan eigendommen dient gemeld te worden</li>
                                </ul>
                            </div>
                            
                            <div>
                                <h3 className="text-lg font-semibold text-gray-900 mb-3">Veiligheidsregels</h3>
                                <ul className="list-disc list-inside space-y-2 text-gray-700">
                                    <li>Wapens altijd behandelen alsof ze geladen zijn</li>
                                    <li>Nooit richten op iets dat je niet wilt raken</li>
                                    <li>Vinger van de trekker tot je klaar bent om te schieten</li>
                                    <li>Wees zeker van je doel en wat erachter ligt</li>
                                    <li>Gehoor- en oogbescherming is verplicht</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {/* Membership Info */}
                    <div className="bg-white rounded-lg shadow-md p-6">
                        <h2 className="text-2xl font-bold text-gray-900 mb-6">Lidmaatschap</h2>
                        
                        <div className="space-y-6">
                            <div>
                                <h3 className="text-lg font-semibold text-gray-900 mb-3">Contributie</h3>
                                <div className="space-y-2">
                                    <div className="flex justify-between">
                                        <span className="text-gray-700">Volwassenen (18+)</span>
                                        <span className="font-medium">€120,00 per jaar</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-gray-700">Jongeren (12-18)</span>
                                        <span className="font-medium">€60,00 per jaar</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-gray-700">Kinderen (onder 12)</span>
                                        <span className="font-medium">€30,00 per jaar</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-gray-700">Familie (4+ personen)</span>
                                        <span className="font-medium">€250,00 per jaar</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <h3 className="text-lg font-semibold text-gray-900 mb-3">Lid Worden</h3>
                                <p className="text-gray-700 mb-3">
                                    Nieuwe leden doorlopen een introductieprogramma van 4 sessies onder begeleiding van een gecertificeerde instructeur.
                                </p>
                                <div className="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <p className="text-blue-800 text-sm">
                                        <strong>Let op:</strong> Voor het lidmaatschap is een VOG (Verklaring Omtrent Gedrag) vereist.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Equipment and Training */}
                <div className="mt-8">
                    <div className="bg-white rounded-lg shadow-md p-6">
                        <h2 className="text-2xl font-bold text-gray-900 mb-6">Uitrusting & Training</h2>
                        
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <h3 className="text-lg font-semibold text-gray-900 mb-3">Beschikbare Uitrusting</h3>
                                <ul className="list-disc list-inside space-y-2 text-gray-700">
                                    <li>Luchtpistolen en -geweren voor beginners</li>
                                    <li>Precisiegeweren voor gevorderden</li>
                                    <li>Gehoor- en oogbescherming (verplicht)</li>
                                    <li>Doelen en houders</li>
                                    <li>Munitie (verkrijgbaar in clubhuis)</li>
                                </ul>
                            </div>
                            
                            <div>
                                <h3 className="text-lg font-semibold text-gray-900 mb-3">Trainingsschema</h3>
                                <div className="space-y-2">
                                    <div className="flex justify-between border-b pb-2">
                                        <span className="font-medium">Beginnerstraining</span>
                                        <span className="text-gray-600">Maandag 19:00-21:00</span>
                                    </div>
                                    <div className="flex justify-between border-b pb-2">
                                        <span className="font-medium">Gevorderdentraining</span>
                                        <span className="text-gray-600">Woensdag 19:00-21:00</span>
                                    </div>
                                    <div className="flex justify-between border-b pb-2">
                                        <span className="font-medium">Jeugdtraining</span>
                                        <span className="text-gray-600">Zaterdag 14:00-16:00</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="font-medium">Vrije training</span>
                                        <span className="text-gray-600">Vrijdag 19:00-22:00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Competition and Events */}
                <div className="mt-8">
                    <div className="bg-white rounded-lg shadow-md p-6">
                        <h2 className="text-2xl font-bold text-gray-900 mb-6">Wedstrijden & Evenementen</h2>
                        
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <h3 className="text-lg font-semibold text-gray-900 mb-3">Wedstrijdklassen</h3>
                                <div className="space-y-3">
                                    <div className="bg-gray-50 rounded-lg p-4">
                                        <h4 className="font-semibold text-gray-900">Recreatief</h4>
                                        <p className="text-sm text-gray-600">Voor nieuwe leden en hobbyschutters</p>
                                    </div>
                                    <div className="bg-gray-50 rounded-lg p-4">
                                        <h4 className="font-semibold text-gray-900">Competitie</h4>
                                        <p className="text-sm text-gray-600">Voor leden die serieus willen wedijveren</p>
                                    </div>
                                    <div className="bg-gray-50 rounded-lg p-4">
                                        <h4 className="font-semibold text-gray-900">Elite</h4>
                                        <p className="text-sm text-gray-600">Voor topschutters en nationale competities</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <h3 className="text-lg font-semibold text-gray-900 mb-3">Jaarlijkse Evenementen</h3>
                                <ul className="list-disc list-inside space-y-2 text-gray-700">
                                    <li>Nieuwjaarstoernooi (Januari)</li>
                                    <li>Lentewedstrijd (April)</li>
                                    <li>Zomerbarbecue (Juli)</li>
                                    <li>Clubkampioenschappen (September)</li>
                                    <li>Sinterklaastoernooi (December)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Documents */}
                <div className="mt-8">
                    <div className="bg-white rounded-lg shadow-md p-6">
                        <h2 className="text-2xl font-bold text-gray-900 mb-6">Belangrijke Documenten</h2>
                        
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div className="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                <div className="flex items-center space-x-3">
                                    <svg className="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fillRule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clipRule="evenodd" />
                                    </svg>
                                    <div>
                                        <h3 className="font-medium text-gray-900">Huishoudelijk Reglement</h3>
                                        <p className="text-sm text-gray-500">PDF - 2.3 MB</p>
                                    </div>
                                </div>
                            </div>

                            <div className="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                <div className="flex items-center space-x-3">
                                    <svg className="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fillRule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clipRule="evenodd" />
                                    </svg>
                                    <div>
                                        <h3 className="font-medium text-gray-900">Aanmeldformulier</h3>
                                        <p className="text-sm text-gray-500">Word - 1.1 MB</p>
                                    </div>
                                </div>
                            </div>

                            <div className="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                <div className="flex items-center space-x-3">
                                    <svg className="w-8 h-8 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fillRule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clipRule="evenodd" />
                                    </svg>
                                    <div>
                                        <h3 className="font-medium text-gray-900">Veiligheidsprotocol</h3>
                                        <p className="text-sm text-gray-500">PDF - 800 KB</p>
                                    </div>
                                </div>
                            </div>

                            <div className="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                <div className="flex items-center space-x-3">
                                    <svg className="w-8 h-8 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fillRule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clipRule="evenodd" />
                                    </svg>
                                    <div>
                                        <h3 className="font-medium text-gray-900">Wedstrijdreglement</h3>
                                        <p className="text-sm text-gray-500">PDF - 1.5 MB</p>
                                    </div>
                                </div>
                            </div>

                            <div className="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                <div className="flex items-center space-x-3">
                                    <svg className="w-8 h-8 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fillRule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clipRule="evenodd" />
                                    </svg>
                                    <div>
                                        <h3 className="font-medium text-gray-900">Privacyverklaring</h3>
                                        <p className="text-sm text-gray-500">PDF - 600 KB</p>
                                    </div>
                                </div>
                            </div>

                            <div className="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                <div className="flex items-center space-x-3">
                                    <svg className="w-8 h-8 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fillRule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clipRule="evenodd" />
                                    </svg>
                                    <div>
                                        <h3 className="font-medium text-gray-900">Jaarverslag 2024</h3>
                                        <p className="text-sm text-gray-500">PDF - 3.2 MB</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
