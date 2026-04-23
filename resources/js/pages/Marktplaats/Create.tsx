import { Head, useForm } from '@inertiajs/react';
import Layout from '@/components/Layout';

export default function MarktplaatsCreate() {
    const { data, setData, post, processing, errors } = useForm({
        title: '',
        description: '',
        category: 'overig',
        price: '',
        condition: '',
        contact_name: '',
        contact_phone: '',
        images: [] as File[],
        is_active: true,
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('marktplaats.store'));
    };

    return (
        <Layout>
            <Head title="Nieuwe Marktplaats Advertentie" />

            <div className="w-[90%] max-w-3xl mx-auto px-4 py-8">
                <h1 className="text-3xl font-bold text-gray-900 mb-2">Nieuwe advertentie</h1>
                <p className="text-gray-600 mb-8">Plaats je advertentie voor andere leden.</p>

                <form onSubmit={submit} className="bg-white rounded-xl shadow p-6 space-y-5">
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Titel</label>
                        <input
                            value={data.title}
                            onChange={(e) => setData('title', e.target.value)}
                            className="w-full rounded-md border border-gray-300 px-3 py-2"
                            placeholder="Bijv. Feinwerkbau luchtpistool"
                        />
                        {errors.title && <p className="text-sm text-red-600 mt-1">{errors.title}</p>}
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Omschrijving</label>
                        <textarea
                            value={data.description}
                            onChange={(e) => setData('description', e.target.value)}
                            className="w-full min-h-32 rounded-md border border-gray-300 px-3 py-2"
                            placeholder="Beschrijf je advertentie"
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
                            {errors.category && <p className="text-sm text-red-600 mt-1">{errors.category}</p>}
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Prijs (EUR)</label>
                            <input
                                value={data.price}
                                onChange={(e) => setData('price', e.target.value)}
                                className="w-full rounded-md border border-gray-300 px-3 py-2"
                                placeholder="Bijv. 250"
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
                                placeholder="Bijv. Zo goed als nieuw"
                            />
                            {errors.condition && <p className="text-sm text-red-600 mt-1">{errors.condition}</p>}
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Telefoon</label>
                            <input
                                value={data.contact_phone}
                                onChange={(e) => setData('contact_phone', e.target.value)}
                                className="w-full rounded-md border border-gray-300 px-3 py-2"
                                placeholder="Bijv. 06..."
                            />
                            {errors.contact_phone && <p className="text-sm text-red-600 mt-1">{errors.contact_phone}</p>}
                        </div>
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Contactnaam</label>
                        <input
                            value={data.contact_name}
                            onChange={(e) => setData('contact_name', e.target.value)}
                            className="w-full rounded-md border border-gray-300 px-3 py-2"
                            placeholder="Leeg laten = je AVG-ledennaam"
                        />
                        {errors.contact_name && <p className="text-sm text-red-600 mt-1">{errors.contact_name}</p>}
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Foto's (max 8)</label>
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
                            {processing ? 'Bezig...' : 'Advertentie plaatsen'}
                        </button>
                    </div>
                </form>
            </div>
        </Layout>
    );
}
