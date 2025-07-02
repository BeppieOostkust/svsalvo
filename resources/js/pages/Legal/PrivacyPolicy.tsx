import { Link } from '@inertiajs/react';
import Layout from '@/components/Layout';

interface LegalDocument {
    id: number;
    title: string;
    content: string;
    version: string;
    effective_date: string;
}

interface Props {
    document: LegalDocument | null;
}

export default function PrivacyPolicy({ document }: Props) {
    return (
        <Layout>
            <div className="min-h-screen">
                <div className="container mx-auto px-4 py-8">
                    <div className="bg-white p-8">
                        {document ? (
                            <>
                                <h1 className="text-3xl font-bold text-black mb-4">
                                    {document.title}
                                </h1>
                                <p className="text-sm text-gray-600 mb-6">
                                    Versie: {document.version} | Effectief vanaf: {new Date(document.effective_date).toLocaleDateString('nl-NL')}
                                </p>
                                <div 
                                    className="prose max-w-none text-black"
                                    dangerouslySetInnerHTML={{ __html: document.content }}
                                />
                            </>
                        ) : (
                            <div className="text-center py-8">
                                <h1 className="text-3xl font-bold text-black mb-4">
                                    Privacy Policy
                                </h1>
                                <p className="text-gray-600">
                                    Er is momenteel geen privacy policy beschikbaar.
                                </p>
                            </div>
                        )}
                        
                        <div className="mt-8 pt-6 border-t border-gray-300">
                            <Link 
                                href="/"
                                className="text-green-600 hover:text-green-700 font-medium"
                            >
                                ← Terug naar home
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
