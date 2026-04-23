import { Head, Link } from '@inertiajs/react';
import Layout from '@/components/Layout';
import { useMemo, useState } from 'react';

interface Listing {
    id: number;
    title: string;
    description: string;
    category: 'wapen' | 'munitie' | 'accessoire' | 'overig';
    price: string | null;
    condition: string | null;
    contact_name: string | null;
    contact_phone: string | null;
    image_urls: string[];
    owner: string | null;
    is_owner: boolean;
    created_at: string | null;
}

interface Props {
    listing: Listing;
    [key: string]: unknown;
}

const categoryLabels: Record<Listing['category'], string> = {
    wapen: 'Wapen',
    munitie: 'Munitie',
    accessoire: 'Accessoire',
    overig: 'Overig',
};

export default function MarktplaatsShow({ listing }: Props) {
    const images = useMemo(() => listing.image_urls ?? [], [listing.image_urls]);
    const [currentImageIndex, setCurrentImageIndex] = useState(0);

    const hasImages = images.length > 0;

    const goPrevious = () => {
        if (!hasImages) {
            return;
        }

        setCurrentImageIndex((prev) => (prev === 0 ? images.length - 1 : prev - 1));
    };

    const goNext = () => {
        if (!hasImages) {
            return;
        }

        setCurrentImageIndex((prev) => (prev === images.length - 1 ? 0 : prev + 1));
    };

    return (
        <Layout>
            <Head title={listing.title} />

            <div className="w-[90%] max-w-6xl mx-auto px-4 py-8">
                <div className="mb-6 flex items-center justify-between gap-4">
                    <Link
                        href={route('marktplaats.index')}
                        className="inline-flex items-center rounded-md border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Terug naar marktplaats
                    </Link>

                    {listing.is_owner && (
                        <Link
                            href={route('marktplaats.edit', listing.id)}
                            className="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
                        >
                            Advertentie bewerken
                        </Link>
                    )}
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <section className="space-y-4">
                        <div className="relative bg-gray-100 rounded-xl overflow-hidden aspect-[4/3] border border-gray-200">
                            {hasImages ? (
                                <img
                                    src={images[currentImageIndex]}
                                    alt={`${listing.title} ${currentImageIndex + 1}`}
                                    className="w-full h-full object-cover"
                                />
                            ) : (
                                <div className="w-full h-full flex items-center justify-center text-gray-500">
                                    Geen foto's beschikbaar
                                </div>
                            )}

                            {images.length > 1 && (
                                <>
                                    <button
                                        type="button"
                                        onClick={goPrevious}
                                        className="absolute left-3 top-1/2 -translate-y-1/2 rounded-full bg-black/65 px-3 py-2 text-white hover:bg-black/80"
                                        aria-label="Vorige foto"
                                    >
                                        {'<'}
                                    </button>
                                    <button
                                        type="button"
                                        onClick={goNext}
                                        className="absolute right-3 top-1/2 -translate-y-1/2 rounded-full bg-black/65 px-3 py-2 text-white hover:bg-black/80"
                                        aria-label="Volgende foto"
                                    >
                                        {'>'}
                                    </button>
                                    <div className="absolute bottom-3 right-3 rounded-full bg-black/65 px-3 py-1 text-xs font-semibold text-white">
                                        {currentImageIndex + 1} / {images.length}
                                    </div>
                                </>
                            )}
                        </div>

                        {images.length > 1 && (
                            <div className="grid grid-cols-5 sm:grid-cols-6 gap-2">
                                {images.map((image, index) => (
                                    <button
                                        key={image}
                                        type="button"
                                        onClick={() => setCurrentImageIndex(index)}
                                        className={`rounded-md overflow-hidden border ${
                                            currentImageIndex === index ? 'border-blue-500 ring-2 ring-blue-200' : 'border-gray-200'
                                        }`}
                                    >
                                        <img src={image} alt={`${listing.title} thumb ${index + 1}`} className="w-full h-16 object-cover" />
                                    </button>
                                ))}
                            </div>
                        )}
                    </section>

                    <section className="bg-white border border-gray-200 rounded-xl p-6 space-y-4">
                        <div className="flex items-start justify-between gap-3">
                            <h1 className="text-3xl font-bold text-gray-900">{listing.title}</h1>
                            <span className="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700">
                                {categoryLabels[listing.category]}
                            </span>
                        </div>

                        <div className="text-2xl font-bold text-blue-700">
                            {listing.price ? `EUR ${listing.price}` : 'Prijs op aanvraag'}
                        </div>

                        {listing.condition && (
                            <p className="text-sm text-gray-700">
                                <span className="font-semibold text-gray-900">Staat:</span> {listing.condition}
                            </p>
                        )}

                        <div className="border-t border-gray-100 pt-4">
                            <h2 className="text-sm font-semibold text-gray-900 mb-2">Omschrijving</h2>
                            <p className="text-gray-700 whitespace-pre-wrap">{listing.description}</p>
                        </div>

                        <div className="border-t border-gray-100 pt-4 space-y-1 text-sm">
                            <p>
                                <span className="font-semibold text-gray-900">Verkoper:</span> {listing.contact_name || listing.owner || 'Onbekend'}
                            </p>
                            {listing.contact_phone && (
                                <p>
                                    <span className="font-semibold text-gray-900">Telefoon:</span> {listing.contact_phone}
                                </p>
                            )}
                        </div>
                    </section>
                </div>
            </div>
        </Layout>
    );
}
