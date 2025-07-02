import { type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import Layout from '@/components/Layout';

interface PublicHomeProps extends SharedData {
    featuredNews: any[];
    upcomingActivities: any[];
    stats: {
        established_year: string;
        member_count: string;
        disciplines: string[];
    };
}

export default function PublicHome() {
    const { featuredNews, upcomingActivities, stats } = usePage<PublicHomeProps>().props;

    return (
        <>
            <Head title="Schietvereniging De Moes - Welkom">
                <link rel="preconnect" href="https://fonts.googleapis.com" />
                <link rel="preconnect" href="https://fonts.gstatic.com" />
                <link href="https://fonts.googleapis.com/css2?family=Geist:wght@100..900&display=swap" rel="stylesheet" />
            </Head>
            
            {/* Navigation Header */}
            <header className="bg-white shadow-sm">
                <div className="container mx-auto px-4">
                    <div className="flex items-center justify-between h-16">
                        <div className="flex items-center">
                            <img src="/logo.svg" alt="SSV De Moes" className="h-8 w-auto mr-3" />
                            <h1 className="text-xl font-bold text-gray-900">SSV De Moes</h1>
                        </div>
                        <div className="flex items-center space-x-4">
                            <Link 
                                href={route("login")}
                                className="text-gray-600 hover:text-green-600 font-medium"
                            >
                                Inloggen
                            </Link>
                            <Link 
                                href={route("register")}
                                className="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 font-medium transition-colors"
                            >
                                Lid Worden
                            </Link>
                        </div>
                    </div>
                </div>
            </header>

            <main className="min-h-screen bg-gray-50">
                {/* Hero Section */}
                <section className="bg-gradient-to-r from-green-700 to-green-600 text-white py-24">
                    <div className="container mx-auto px-4">
                        <div className="max-w-4xl mx-auto text-center">
                            <h1 className="text-6xl font-bold mb-6">
                                Welkom bij<br />Schietvereniging De Moes
                            </h1>
                            <p className="text-xl mb-8 text-green-100 max-w-2xl mx-auto leading-relaxed">
                                Ontdek de kunst van de schietsport bij een van de gezelligste 
                                en meest professionele schietverenigingen van Nederland. 
                                Al sinds {stats.established_year} staan veiligheid, kwaliteit en kameraadschap bij ons centraal.
                            </p>
                            <div className="flex flex-wrap justify-center gap-4">
                                <Link 
                                    href="/register" 
                                    className="bg-white text-green-700 px-8 py-4 rounded-lg font-bold text-lg hover:bg-green-50 transition-colors shadow-lg"
                                >
                                    Word Lid →
                                </Link>
                                <a 
                                    href="#meer-info" 
                                    className="border-2 border-white text-white px-8 py-4 rounded-lg font-bold text-lg hover:bg-white hover:text-green-700 transition-colors"
                                >
                                    Meer Informatie
                                </a>
                            </div>
                        </div>
                    </div>
                </section>

                {/* Stats Section */}
                <section className="py-16 bg-white">
                    <div className="container mx-auto px-4">
                        <div className="max-w-6xl mx-auto">
                            <div className="grid md:grid-cols-3 gap-8 text-center">
                                <div className="p-6">
                                    <div className="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg className="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <h3 className="text-3xl font-bold text-gray-900 mb-2">Sinds {stats.established_year}</h3>
                                    <p className="text-gray-600">Jarenlange ervaring in de schietsport</p>
                                </div>
                                <div className="p-6">
                                    <div className="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg className="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                    <h3 className="text-3xl font-bold text-gray-900 mb-2">{stats.member_count}</h3>
                                    <p className="text-gray-600">Actieve leden van alle niveaus</p>
                                </div>
                                <div className="p-6">
                                    <div className="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg className="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                        </svg>
                                    </div>
                                    <h3 className="text-3xl font-bold text-gray-900 mb-2">{stats.disciplines.length}</h3>
                                    <p className="text-gray-600">Verschillende schietsport disciplines</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {/* About Section */}
                <section id="meer-info" className="py-16 bg-gray-50">
                    <div className="container mx-auto px-4">
                        <div className="max-w-6xl mx-auto">
                            <div className="grid md:grid-cols-2 gap-12 items-center">
                                <div>
                                    <h2 className="text-4xl font-bold mb-6 text-gray-900">
                                        Waarom kiezen voor De Moes?
                                    </h2>
                                    <div className="space-y-6">
                                        <div className="flex items-start">
                                            <div className="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-4 mt-1">
                                                <svg className="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4" />
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 className="text-xl font-semibold text-gray-900 mb-2">Veiligheid voorop</h3>
                                                <p className="text-gray-600">Uitgebreide veiligheidstraining en continue begeleiding door ervaren instructeurs.</p>
                                            </div>
                                        </div>
                                        <div className="flex items-start">
                                            <div className="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-4 mt-1">
                                                <svg className="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4" />
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 className="text-xl font-semibold text-gray-900 mb-2">Voor alle niveaus</h3>
                                                <p className="text-gray-600">Van complete beginner tot competitieschutter - iedereen is welkom.</p>
                                            </div>
                                        </div>
                                        <div className="flex items-start">
                                            <div className="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-4 mt-1">
                                                <svg className="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4" />
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 className="text-xl font-semibold text-gray-900 mb-2">Moderne faciliteiten</h3>
                                                <p className="text-gray-600">State-of-the-art schietsportfaciliteiten met de nieuwste apparatuur.</p>
                                            </div>
                                        </div>
                                        <div className="flex items-start">
                                            <div className="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-4 mt-1">
                                                <svg className="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4" />
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 className="text-xl font-semibold text-gray-900 mb-2">Gezellige vereniging</h3>
                                                <p className="text-gray-600">Een hechte gemeenschap waar sport en gezelligheid hand in hand gaan.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div className="bg-white rounded-lg shadow-lg p-8">
                                    <h3 className="text-2xl font-bold text-gray-900 mb-6">Disciplines</h3>
                                    <div className="space-y-4">
                                        {stats.disciplines.map((discipline, index) => (
                                            <div key={index} className="flex items-center p-3 bg-gray-50 rounded-lg">
                                                <div className="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                                    <svg className="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4" />
                                                    </svg>
                                                </div>
                                                <span className="font-medium text-gray-900">{discipline}</span>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {/* Call to Action */}
                <section className="py-20 bg-gradient-to-r from-green-600 to-green-700 text-white">
                    <div className="container mx-auto px-4">
                        <div className="max-w-4xl mx-auto text-center">
                            <h2 className="text-4xl font-bold mb-6">
                                Klaar om te beginnen?
                            </h2>
                            <p className="text-xl mb-8 text-green-100">
                                Word lid van onze gemeenschap en ontdek de passie voor schietsport.
                                We staan klaar om je te begeleiden in deze fascinerende sport!
                            </p>
                            <div className="flex flex-wrap justify-center gap-4">
                                <Link 
                                    href="/register" 
                                    className="bg-white text-green-700 px-8 py-4 rounded-lg font-bold text-lg hover:bg-green-50 transition-colors shadow-lg"
                                >
                                    Schrijf je in als lid
                                </Link>
                                <a 
                                    href="mailto:info@ssvdemoes.nl" 
                                    className="border-2 border-white text-white px-8 py-4 rounded-lg font-bold text-lg hover:bg-white hover:text-green-700 transition-colors"
                                >
                                    Stel een vraag
                                </a>
                            </div>
                        </div>
                    </div>
                </section>
            </main>
        </>
    );
}
