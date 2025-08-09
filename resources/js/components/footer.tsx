import { Mail, Phone, MapPin } from 'lucide-react';
import { Link } from '@inertiajs/react';

export default function Footer() {
    return (
        <footer className="border-t-4 border-green-600 bg-[#0c1220] text-white">
            <div className="mx-auto w-full max-w-7xl px-4 py-10 md:px-8">
                <div className="grid gap-8 md:grid-cols-3">
                    {/* Logo & About */}
                    <div className="space-y-4">
                        <div className="flex items-center gap-2">
                            <img src="/images/logo_white.png" alt="SSV De Moes" className="h-8 w-auto" />
                            <span className="text-lg font-semibold">SSV De Moes</span>
                        </div>
                        <p className="max-w-sm text-sm text-gray-400">
                            Al sinds 1997 de plek voor veilige en professionele schietsport in de regio.
                        </p>
                    </div>

                    {/* Contact */}
                    <div>
                        <h3 className="mb-4 font-semibold">Contact</h3>
                        <ul className="space-y-2 text-sm text-gray-300">
                            <li className="flex items-center gap-2">
                                <Mail className="h-4 w-4" /> info@ssvdemoes.nl
                            </li>
                            <li className="flex items-start gap-2">
                                <MapPin className="mt-0.5 h-4 w-4" />
                                <span>
                                    Schietbaan De Moes
                                    <br />5107 RD Dongen
                                    <br />De Schacht 5
                                </span>
                            </li>
                        </ul>
                    </div>

                    {/* Links */}
                    <div>
                        <h3 className="mb-4 font-semibold">Links</h3>
                        <ul className="space-y-2 text-sm text-gray-300">
                            <li>
                                <Link href={route('login')} className="hover:text-white hover:underline">
                                    Inloggen
                                </Link>
                            </li>
                            <li>
                                <Link href={route('register')} className="hover:text-white hover:underline">
                                    Registreren
                                </Link>
                            </li>
                            <li>
                                <a href="https://www.knsa.nl" target="_blank" rel="noopener noreferrer" className="hover:text-white hover:underline">
                                    KNSA
                                </a>
                            </li>
                            <li>
                                <Link href={route('privacy-policy')} className="hover:text-white hover:underline">
                                    Privacy Policy
                                </Link>
                            </li>
                            <li>
                                <Link href={route('terms-conditions')} className="hover:text-white hover:underline">
                                    Algemene Voorwaarden
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>

                <div className="mt-8 border-t border-gray-700 pt-4 text-center text-xs text-gray-400">
                    © {new Date().getFullYear()} Schietvereniging De Moes. Alle rechten voorbehouden.
                </div>
            </div>
        </footer>
    );
} 