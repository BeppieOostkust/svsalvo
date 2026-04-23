import { Head, useForm } from '@inertiajs/react';
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
    is_active: boolean;
}

interface Props {
    listing: Listing;
    [key: string]: unknown;
}

export default function MarktplaatsEdit({ listing }: Props) {
    const { data, setData, post, processing, errors } = useForm({
        _method: 'put',
        title: listing.title,
        description: listing.description,
        category: listing.category,
        price: listing.price ?? '',
        condition: listing.condition ?? '',
        contact_name: listing.contact_name ?? '',
        contact_phone: listing.contact_phone ?? '',
        images: [] as File[],
        is_active: listing.is_active,
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('marktplaats.update', listing.id));
    };

    return (
        <Layout>
            <Head title="Advertentie Bewerken" />

            <div className="w-[90%] max-w-3xl mx-auto px-4 py-8">
                <h1 className="text-3xl font-bold text-gray-900 mb-2">Advertentie bewerken</h1>
                <p className="text-gray-600 mb-8">Pas je advertentie aan en sla op.</p>

                <form onSubmit={submit} className="bg-white rounded-xl shadow p-6 space-y-5">
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Titel</label>
                        <input
                            value={data.title}
                            onChange={(e) => setData('title', e.target.value)}
                            className="w-full rounded-md border border-gray-300 px-3 py-2"
                        />
                        {errors.title && <p className="text-sm text-red-600 mt-1">{errors.title}</p>}
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Omschrijving</label>
                        <textarea
                            value={data.description}
                            onChange={(e) => setData('description', e.target.value)}
                            className="w-full min-h-32 rounded-md border border-gray-300 px-3 py-2"
                        />
                        {errors.description && <p className="text-sm text-red-600 mt-1">{errors.description}</p>}
                    </div>

                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Categorie</label>
                            <select
                                value={data.category}
                                onChange={(e) => setData('category', e.target.value)}
                                className="w-full rounded-md border border-gray-300 px-3 py-2"
                            >
                                <option value="wapen">Wapen</option>
                                <option value="munitie">Munitie</option>
                                <option value="accessoire">Accessoire</option>
                                <option value="overig">Overig</option>
                            </select>
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Prijs (EUR)</label>
                            <input
                                value={data.price}
                                onChange={(e) => setData('price', e.target.value)}
                                className="w-full rounded-md border border-gray-300 px-3 py-2"
                            />
                            {errors.price && <p className="text-sm text-red-600 mt-1">{errors.price}</p>}
                        </div>
                    </div>

                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Staat</label>
                            <input
                                value={data.condition}
                                onChange={(e) => setData('condition', e.target.value)}
                                className="w-full rounded-md border border-gray-300 px-3 py-2"
                            />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Telefoon</label>
                            <input
                                value={data.contact_phone}
                                onChange={(e) => setData('contact_phone', e.target.value)}
                                className="w-full rounded-md border border-gray-300 px-3 py-2"
                            />
                        </div>
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Contactnaam</label>
                        <input
                            value={data.contact_name}
                            onChange={(e) => setData('contact_name', e.target.value)}
                            className="w-full rounded-md border border-gray-300 px-3 py-2"
                        />
                    </div>

                    {listing.image_urls.length > 0 && (
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Huidige foto's</label>
                            <div className="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                {listing.image_urls.map((url, index) => (
                                    <img key={url} src={url} alt={`${listing.title} ${index + 1}`} className="h-28 w-full object-cover rounded-md border" />
                                ))}
                            </div>
                        </div>
                    )}

                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Nieuwe foto's (optioneel, vervangt huidige foto's)</label>
                        <input
                            type="file"
                            accept="image/*"
                            multiple
                            onChange={(e) => setData('images', Array.from(e.target.files ?? []))}
                            className="w-full rounded-md border border-gray-300 px-3 py-2"
                        />
                        <p className="text-xs text-gray-500 mt-1">Geselecteerd: {data.images.length} bestand(en)</p>
                        {errors.images && <p className="text-sm text-red-600 mt-1">{errors.images}</p>}
                    </div>

                    <label className="flex items-center gap-2 text-sm text-gray-700">
                        <input
                            type="checkbox"
                            checked={data.is_active}
                            onChange={(e) => setData('is_active', e.target.checked)}
                        />
                        Advertentie actief publiceren
                    </label>

                    <div className="flex items-center gap-3 pt-2">
                        <button
                            type="submit"
                            disabled={processing}
                            className="rounded-md bg-blue-600 px-4 py-2 font-medium text-white hover:bg-blue-700 disabled:opacity-60"
                        >
                            {processing ? 'Opslaan...' : 'Wijzigingen opslaan'}
                        </button>
                    </div>
                </form>
            </div>
        </Layout>
    );
}
