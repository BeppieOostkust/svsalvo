import { Head, usePage } from '@inertiajs/react';
import Layout from '@/components/Layout';

interface ClubWeapon {
    id: number;
    name: string;
    weapon_type: 'GKG' | 'KKG' | 'KKP' | 'GKP';
    image: string;
    image_url: string | null;
}

interface ClubWeaponPageProps {
    clubWeapons: ClubWeapon[];
    [key: string]: unknown;
}

export default function ClubwapensIndex() {
    const { clubWeapons } = usePage<ClubWeaponPageProps>().props;

    const getBadgeColor = (type: ClubWeapon['weapon_type']): string => {
        switch (type) {
            case 'GKG':
                return 'bg-blue-100 text-blue-800';
            case 'KKG':
                return 'bg-green-100 text-green-800';
            case 'KKP':
                return 'bg-amber-100 text-amber-800';
            case 'GKP':
                return 'bg-red-100 text-red-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    };

    return (
        <Layout>
            <Head title="Clubwapens" />

            <div className="w-[90%] mx-auto px-4 py-8">
                <div className="mb-8">
                    <h1 className="text-3xl font-bold text-gray-900 mb-2">Clubwapens</h1>
                    <p className="text-gray-600">
                        Overzicht van de clubwapens die beschikbaar zijn voor leden.
                    </p>
                </div>

                {clubWeapons.length === 0 ? (
                    <div className="bg-white rounded-lg shadow p-8 text-center text-gray-600">
                        Er zijn nog geen clubwapens toegevoegd.
                    </div>
                ) : (
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        {clubWeapons.map((weapon) => (
                            <article key={weapon.id} className="bg-white rounded-xl shadow hover:shadow-md transition-shadow overflow-hidden">
                                <div className="aspect-[4/3] bg-gray-100">
                                    {weapon.image_url ? (
                                        <img
                                            src={weapon.image_url}
                                            alt={weapon.name}
                                            className="w-full h-full object-cover"
                                        />
                                    ) : (
                                        <div className="w-full h-full flex items-center justify-center text-gray-500">
                                            Geen afbeelding
                                        </div>
                                    )}
                                </div>

                                <div className="p-4">
                                    <div className="flex items-center justify-between gap-3">
                                        <h2 className="text-lg font-semibold text-gray-900 truncate">{weapon.name}</h2>
                                        <span className={`inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ${getBadgeColor(weapon.weapon_type)}`}>
                                            {weapon.weapon_type}
                                        </span>
                                    </div>
                                </div>
                            </article>
                        ))}
                    </div>
                )}
            </div>
        </Layout>
    );
}
