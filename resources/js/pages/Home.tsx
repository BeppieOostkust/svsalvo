import { type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import Layout from '@/components/Layout';
import { GeistSans } from 'geist/font/sans';
import { GeistMono } from 'geist/font/mono';

interface HomeProps extends SharedData {
    latestNews: any[];
    featuredNews: any;
    upcomingActivities: any[];
    upcomingMatches: any[];
}

export default function Home() {
    const { auth, latestNews, featuredNews, upcomingActivities, upcomingMatches } = usePage<HomeProps>().props;

    return (
        <Layout>
            <Head title="Schietvereniging De Moes - Home">
                <link rel="preconnect" href="https://fonts.googleapis.com" />
                <link rel="preconnect" href="https://fonts.gstatic.com" />
                <link href="https://fonts.googleapis.com/css2?family=Geist:wght@100..900&display=swap" rel="stylesheet" />
            </Head>
            
            <main className="bg-gray-50">{/* remove min-h-screen as Layout handles this */}
                {/* Hero Section */}
                <section className="bg-gradient-to-r from-green-700 to-green-600 text-white py-20">
                    <div className="container mx-auto px-4">
                        <div className="max-w-4xl mx-auto text-center">
                            <h1 className="text-3xl md:text-5xl font-bold mb-6">
                                Welkom bij<br />Schietvereniging Salvo
                            </h1>
                            <p className="text-xl mb-8 text-green-100">
                                Een gezellige en professionele schietsportvereniging waar veiligheid, 
                                kwaliteit en kameraadschap centraal staan.
                            </p>
                            <div className="flex flex-wrap justify-center gap-4">
                                <Link 
                                    href={route("activiteiten")}
                                    className="bg-white text-green-700 px-8 py-3 rounded-lg font-semibold hover:bg-green-50 transition-colors"
                                >
                                    Bekijk Activiteiten
                                </Link>
                                <Link 
                                    href={route("organisatie")} 
                                    className="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-green-700 transition-colors"
                                >
                                    Contact
                                </Link>
                            </div>
                        </div>
                    </div>
                </section>

                {/* Featured News */}
                {featuredNews && (
                    <section className="py-16 bg-white">
                        <div className="container mx-auto px-4">
                            <div className="max-w-6xl mx-auto">
                                <div className="grid md:grid-cols-2 gap-12 items-center">
                                    <div>
                                        <span className="inline-block bg-red-600 text-white px-3 py-1 rounded-full text-sm font-semibold mb-4">
                                            BELANGRIJK NIEUWS
                                        </span>
                                        <h2 className="text-3xl font-bold mb-4 text-gray-900">
                                            {featuredNews.title}
                                        </h2>
                                        <p className="text-gray-600 mb-6 text-lg">
                                            {featuredNews.excerpt || featuredNews.content?.substring(0, 200) + '...'}
                                        </p>
                                        <Link 
                                            href={`/nieuws/${featuredNews.slug}`}
                                            className="inline-flex items-center text-blue-600 font-semibold hover:text-blue-800"
                                        >
                                            Lees meer →
                                        </Link>
                                    </div>
                                    {featuredNews.featured_image && (
                                        <div>
                                            <img 
                                                src={featuredNews.featured_image} 
                                                alt={featuredNews.title}
                                                className="w-full h-80 object-cover rounded-lg shadow-lg"
                                            />
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>
                    </section>
                )}

                {/* Quick Overview Grid */}
                <section className="py-16 bg-gray-50">
                    <div className="container mx-auto px-4">
                        <div className="max-w-6xl mx-auto">
                            <div className="grid md:grid-cols-3 gap-8">
                                
                                {/* Latest News */}
                                <div className="bg-white rounded-lg shadow-md p-6">
                                    <div className="flex items-center mb-4">
                                        <div className="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                                            <svg className="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15" />
                                            </svg>
                                        </div>
                                        <h3 className="text-xl font-bold text-gray-900">Laatste Nieuws</h3>
                                    </div>
                                    <div className="space-y-4">
                                        {latestNews.slice(0, 3).map((article, index) => (
                                            <div key={index} className="border-b border-gray-100 pb-3 last:border-b-0">
                                                <Link href={`/nieuws/${article.slug}`} className="block hover:text-blue-600">
                                                    <h4 className="font-semibold text-sm mb-1">{article.title}</h4>
                                                    <p className="text-xs text-gray-500">
                                                        {new Date(article.published_at).toLocaleDateString('nl-NL')}
                                                    </p>
                                                </Link>
                                            </div>
                                        ))}
                                    </div>
                                    <div className="mt-4">
                                        <Link 
                                            href="/nieuws" 
                                            className="text-blue-600 text-sm font-semibold hover:text-blue-800"
                                        >
                                            Alle nieuws →
                                        </Link>
                                    </div>
                                </div>

                                {/* Upcoming Activities */}
                                <div className="bg-white rounded-lg shadow-md p-6">
                                    <div className="flex items-center mb-4">
                                        <div className="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                                            <svg className="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <h3 className="text-xl font-bold text-gray-900">Komende Activiteiten</h3>
                                    </div>
                                    <div className="space-y-4">
                                        {upcomingActivities.slice(0, 3).map((activity, index) => (
                                            <div key={index} className="border-b border-gray-100 pb-3 last:border-b-0">
                                                <Link href={`/activiteiten/${activity.slug}`} className="block hover:text-green-600">
                                                    <h4 className="font-semibold text-sm mb-1">{activity.title}</h4>
                                                    <p className="text-xs text-gray-500">
                                                        {new Date(activity.start_date).toLocaleDateString('nl-NL')}
                                                        {activity.start_time && ` om ${activity.start_time}`}
                                                    </p>
                                                </Link>
                                            </div>
                                        ))}
                                    </div>
                                    <div className="mt-4">
                                        <Link 
                                            href="/activiteiten" 
                                            className="text-green-600 text-sm font-semibold hover:text-green-800"
                                        >
                                            Alle activiteiten →
                                        </Link>
                                    </div>
                                </div>

                                {/* Upcoming Matches */}
                                <div className="bg-white rounded-lg shadow-md p-6">
                                    <div className="flex items-center mb-4">
                                        <div className="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mr-4">
                                            <svg className="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                            </svg>
                                        </div>
                                        <h3 className="text-xl font-bold text-gray-900">Komende Wedstrijden</h3>
                                    </div>
                                    <div className="space-y-4">
                                        {upcomingMatches.slice(0, 3).map((match, index) => (
                                            <div key={index} className="border-b border-gray-100 pb-3 last:border-b-0">
                                                <Link href={`/wedstrijden/${match.slug || match.id}`} className="block hover:text-red-600">
                                                    <h4 className="font-semibold text-sm mb-1">{match.naam || match.title}</h4>
                                                    <p className="text-xs text-gray-500">
                                                        {new Date(match.start_datum).toLocaleDateString('nl-NL')}
                                                    </p>
                                                </Link>
                                            </div>
                                        ))}
                                    </div>
                                    <div className="mt-4">
                                        <Link 
                                            href="/wedstrijden" 
                                            className="text-red-600 text-sm font-semibold hover:text-red-800"
                                        >
                                            Alle wedstrijden →
                                        </Link>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </section>

                {/* Call to Action */}
                <section className="py-16 bg-green-700 text-white">
                    <div className="container mx-auto px-4">
                        <div className="max-w-4xl mx-auto text-center">
                            <h2 className="text-3xl font-bold mb-4">
                                Interesse in schietsport?
                            </h2>
                            <p className="text-xl mb-8 text-green-100">
                                Kom eens langs tijdens onze open dag of neem contact met ons op 
                                voor meer informatie over lidmaatschap.
                            </p>
                            <div className="flex flex-wrap justify-center gap-4">
                                <a 
                                    href="mailto:secretaris@svsalvo.info"
                                    className="bg-white text-green-700 px-8 py-3 rounded-lg font-semibold hover:bg-green-50 transition-colors"
                                >
                                    Contact Opnemen
                                </a>
                                <Link 
                                    href={route('downloads')} 
                                    className="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-green-700 transition-colors"
                                >
                                    Informatiemateriaal
                                </Link>
                            </div>
                        </div>
                    </div>
                </section>
            </main>
        </ Layout>
    );
}
