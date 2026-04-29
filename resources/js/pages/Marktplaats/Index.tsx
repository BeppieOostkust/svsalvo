import { Head, Link, router, usePage } from '@inertiajs/react';
import Layout from '@/components/Layout';

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
    image_url: string | null;
    owner: string | null;
    is_owner: boolean;
    created_at: string | null;
}

interface Props {
    listings: Listing[];
    [key: string]: unknown;
}

const categoryLabels: Record<Listing['category'], string> = {
    wapen: 'Wapen',
    munitie: 'Munitie',
    accessoire: 'Accessoire',
    overig: 'Overig',
};

const categoryColors: Record<Listing['category'], string> = {
    wapen: 'bg-red-100 text-red-700',
    munitie: 'bg-orange-100 text-orange-700',
    accessoire: 'bg-blue-100 text-blue-700',
    overig: 'bg-gray-100 text-gray-700',
};

export default function MarktplaatsIndex() {
    const { listings } = usePage<Props>().props;

    return (
        <Layout>
            <Head title="Leden Marktplaats" />

            <div className="w-[90%] mx-auto px-4 py-8">
                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                    <div>
                        <h1 className="text-3xl font-bold text-gray-900">Leden Marktplaats</h1>
                        <p className="text-gray-600 mt-2">
                            Koop en verkoop spullen onder leden. Alleen zichtbaar voor ingelogde leden.
                        </p>
                    </div>
                    <Link
                        href={route('marktplaats.create')}
                        className="inline-flex items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-white font-medium hover:bg-blue-700"
                    >
                        Nieuwe advertentie
                    </Link>
                </div>

                {listings.length === 0 ? (
                    <div className="bg-white rounded-lg shadow p-8 text-center text-gray-600">
                        Er staan nog geen advertenties online.
                    </div>
                ) : (
                    <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        {listings.map((listing) => (
                            <article
                                key={listing.id}
                                className="bg-white rounded-xl shadow overflow-hidden border border-gray-100 cursor-pointer hover:shadow-md transition-shadow"
                                onClick={() => router.visit(route('marktplaats.show', listing.id))}
                            >
                                <div className="aspect-[4/3] bg-gray-100">
                                    {listing.image_urls.length > 0 ? (
                                        <div className="relative w-full h-full">
                                            <img src={listing.image_urls[0]} alt={listing.title} className="w-full h-full object-cover" />
                                            {listing.image_urls.length > 1 && (
                                                <div className="absolute top-2 right-2 rounded-full bg-black/70 px-2 py-1 text-xs font-semibold text-white">
                                                    +{listing.image_urls.length - 1} foto's
                                                </div>
                                            )}
                                        </div>
                                    ) : (
                                        <div className="w-full h-full flex items-center justify-center text-gray-500">Geen afbeelding</div>
                                    )}
                                </div>
                                <div className="p-4 space-y-3">
                                    <div className="flex items-start justify-between gap-3">
                                        <h2 className="font-semibold text-gray-900 leading-tight">{listing.title}</h2>
                                        <span className={`inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ${categoryColors[listing.category]}`}>
                                            {categoryLabels[listing.category]}
                                        </span>
                                    </div>

                                    <p className="text-sm text-gray-600 line-clamp-3">{listing.description}</p>

                                    <div className="text-sm space-y-1">
                                        <div>
                                            <span className="font-semibold text-gray-900">Prijs: </span>
                                            <span className="text-gray-700">{listing.price ? `EUR ${listing.price}` : 'Op aanvraag'}</span>
                                        </div>
                                        {listing.condition && (
                                            <div>
                                                <span className="font-semibold text-gray-900">Staat: </span>
                                                <span className="text-gray-700">{listing.condition}</span>
                                            </div>
                                        )}
                                        <div>
                                            <span className="font-semibold text-gray-900">Contact: </span>
                                            <span className="text-gray-700">{listing.contact_name || listing.owner || 'Onbekend'}</span>
                                        </div>
                                        {listing.contact_phone && (
                                            <div>
                                                <span className="font-semibold text-gray-900">Telefoon: </span>
                                                <span className="text-gray-700">{listing.contact_phone}</span>
                                            </div>
                                        )}
                                    </div>

                                    {listing.is_owner && (
                                        <div className="pt-2 flex items-center gap-2">
                                            <Link
                                                href={route('marktplaats.edit', listing.id)}
                                                onClick={(e) => e.stopPropagation()}
                                                className="inline-flex items-center rounded-md border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50"
                                            >
                                                Bewerken
                                            </Link>
                                            <Link
                                                href={route('marktplaats.destroy', listing.id)}
                                                method="delete"
                                                as="button"
                                                onClick={(e) => e.stopPropagation()}
                                                className="inline-flex items-center rounded-md border border-red-300 px-3 py-1.5 text-sm font-medium text-red-700 hover:bg-red-50"
                                            >
                                                Verwijderen
                                            </Link>
                                        </div>
                                    )}
                                </div>
                            </article>
                        ))}
                    </div>
                )}
            </div>
        </Layout>
    );
}
