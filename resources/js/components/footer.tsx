import { type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/react';

interface PublicHomeProps extends SharedData {
    stats: {
        established_year: string;
        member_count: string;
        disciplines: string[];
    };
}

export default function Footer() {
    const { stats } = usePage<PublicHomeProps>().props;
    return (
    <footer className="bg-gray-900 text-white py-12">
                <div className="container mx-auto px-4">
                    <div className="max-w-6xl mx-auto grid md:grid-cols-3 gap-8">
                        <div>
                            <div className="flex items-center mb-4">
                                <img src="/logo.svg" alt="SSV De Moes" className="h-8 w-auto mr-3" />
                                <h3 className="text-xl font-bold">SSV De Moes</h3>
                            </div>
                            <p className="text-gray-400 mb-4">
                                Al sinds {stats.established_year} de plek voor veilige en professionele schietsport in de regio.
                            </p>
                        </div>
                        <div>
                            <h4 className="text-lg font-semibold mb-4">Contact</h4>
                            <div className="space-y-2 text-gray-400">
                                <p>📧 info@ssvdemoes.nl</p>
                                <p>📞 +31 (0)6 12345678</p>
                                <p>📍 Schietbaan De Moes<br />1234 AB Voorbeeld</p>
                            </div>
                        </div>
                        <div>
                            <h4 className="text-lg font-semibold mb-4">Links</h4>
                            <div className="space-y-2">
                                <div><Link href="/login" className="text-gray-400 hover:text-white">Inloggen</Link></div>
                                <div><Link href="/register" className="text-gray-400 hover:text-white">Lid worden</Link></div>
                                <div><a href="https://www.knsa.nl" target="_blank" rel="noopener noreferrer" className="text-gray-400 hover:text-white">KNSA</a></div>
                            </div>
                        </div>
                    </div>
                    <div className="max-w-6xl mx-auto border-t border-gray-700 pt-8 mt-8 text-center text-gray-400">
                        <p>&copy; {new Date().getFullYear()} Schietvereniging De Moes. Alle rechten voorbehouden.</p>
                    </div>
                </div>
            </footer>
    );
}