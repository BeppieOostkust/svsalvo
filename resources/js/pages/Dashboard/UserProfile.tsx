import React from 'react';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import Header from '@/components/header';
import { format } from 'date-fns';
import Layout from '@/components/Layout';

interface User {
    id: number;
    name: string;
    avg_name: string;
    email: string;
    first_name: string | null;
    last_name: string | null;
    date_of_birth: string | null;
    phone: string | null;
    address: string | null;
    city: string | null;
    postal_code: string | null;
    country: string;
    position: string | null;
    bio: string | null;
    profile_image: string | null;
    profile_image_url: string | null;
    member_since: string | null;
    preferred_discipline: string | null;
    license_number: string | null;
    license_expiry: string | null;
    is_admin: boolean;
    is_active_member: boolean;
    show_contact_info: boolean;
    show_scores_public: boolean;
    show_full_name: boolean;
    show_contact_on_members_page: boolean;
    show_in_participants: boolean;
}

interface PageProps {
    user: User;
    [key: string]: any;
}

export default function UserProfile() {
    const { user, flash } = usePage<PageProps>().props;

    const { data, setData, post, processing, errors } = useForm({
        first_name: user.first_name || '',
        last_name: user.last_name || '',
        phone: user.phone || '',
        address: user.address || '',
        city: user.city || '',
        postal_code: user.postal_code || '',
        country: user.country || 'Nederland',
        date_of_birth: user.date_of_birth ? format(new Date(user.date_of_birth), 'yyyy-MM-dd') : '',
        preferred_discipline: user.preferred_discipline || '',
        show_contact_info: user.show_contact_info,
        show_scores_public: user.show_scores_public,
        show_full_name: user.show_full_name,
        show_contact_on_members_page: user.show_contact_on_members_page,
        show_in_participants: user.show_in_participants,
        profile_image: null as File | null,
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/profile');
    };

    const formatDate = (dateString: string | null) => {
        if (!dateString) return 'Niet opgegeven';
        try {
            return format(new Date(dateString), 'd MMMM yyyy');
        } catch {
            return dateString;
        }
    };

    return (
        <Layout>
            <Head title="Profiel bewerken" />
            
            
            <div className="w-[90%] mx-auto px-4 py-8">
                {/* Back Button */}
                <div className="mb-6">
                    <Link
                        href="/dashboard"
                        className="inline-flex items-center text-blue-600 hover:text-blue-800"
                    >
                        ← Terug naar dashboard
                    </Link>
                </div>

                {/* Success Message */}
                {flash?.success && (
                    <div className="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <p className="text-green-800">{flash.success}</p>
                    </div>
                )}

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {/* Profile Info (Read-only) */}
                    <div className="lg:col-span-1">
                        <div className="bg-white rounded-lg shadow-md p-6">
                            <h2 className="text-lg font-semibold mb-4">Account Informatie</h2>
                            <div className="space-y-4">
                                <div>
                                    <label className="text-sm font-medium text-gray-500">Gebruikersnaam</label>
                                    <p className="text-gray-900">{user.name}</p>
                                </div>
                                <div>
                                    <label className="text-sm font-medium text-gray-500">AVG Ledennaam</label>
                                    <p className="text-gray-900">{user.avg_name}</p>
                                </div>
                                <div>
                                    <label className="text-sm font-medium text-gray-500">E-mailadres</label>
                                    <p className="text-gray-900">{user.email}</p>
                                </div>
                                {user.position && (
                                    <div>
                                        <label className="text-sm font-medium text-gray-500">Functie</label>
                                        <p className="text-gray-900">{user.position}</p>
                                    </div>
                                )}
                                {user.member_since && (
                                    <div>
                                        <label className="text-sm font-medium text-gray-500">Lid sinds</label>
                                        <p className="text-gray-900">{formatDate(user.member_since)}</p>
                                    </div>
                                )}
                                {user.license_number && (
                                    <div>
                                        <label className="text-sm font-medium text-gray-500">Licentienummer</label>
                                        <p className="text-gray-900">{user.license_number}</p>
                                        {user.license_expiry && (
                                            <p className="text-sm text-gray-500">
                                                Verloopt: {formatDate(user.license_expiry)}
                                            </p>
                                        )}
                                    </div>
                                )}
                            </div>
                            
                            <div className="mt-6 p-4 bg-blue-50 rounded-lg">
                                <p className="text-sm text-blue-800">
                                    <strong>Let op:</strong> Wijzigingen aan account informatie, functie, lidmaatschap 
                                    en licentiegegevens kunnen alleen door een beheerder worden gemaakt. 
                                    Neem contact op als deze gegevens onjuist zijn.
                                </p>
                            </div>
                        </div>
                    </div>

                    {/* Edit Form */}
                    <div className="lg:col-span-2">
                        <div className="bg-white rounded-lg shadow-md p-6">
                            <h2 className="text-xl font-semibold mb-6">Persoonlijke Gegevens Bewerken</h2>
                            
                            <form onSubmit={handleSubmit} className="space-y-6">
                                {/* Profile Image Upload */}
                                <div className="mb-6">
                                    <h3 className="text-lg font-medium text-gray-900 mb-4">Profielfoto</h3>
                                    <div className="flex items-center space-x-6">
                                        {/* Current Profile Image */}
                                        <div className="flex-shrink-0">
                                            {user.profile_image_url ? (
                                                <img
                                                    src={user.profile_image_url}
                                                    alt="Profile"
                                                    className="w-24 h-24 rounded-full object-cover border-2 border-gray-300"
                                                />
                                            ) : (
                                                <div className="w-24 h-24 rounded-full bg-gray-300 flex items-center justify-center border-2 border-gray-300">
                                                    <span className="text-gray-600 font-semibold text-2xl">
                                                        {(user.first_name?.[0] || user.name[0]).toUpperCase()}
                                                    </span>
                                                </div>
                                            )}
                                        </div>
                                        
                                        {/* Upload Controls */}
                                        <div className="flex-1">
                                            <label className="block text-sm font-medium text-gray-700 mb-2">
                                                Nieuwe profielfoto uploaden
                                            </label>
                                            <input
                                                type="file"
                                                accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                                                onChange={(e) => {
                                                    const file = e.target.files?.[0] || null;
                                                    setData('profile_image', file);
                                                }}
                                                className="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                            />
                                            {errors.profile_image && (
                                                <p className="text-red-500 text-sm mt-1">{errors.profile_image}</p>
                                            )}
                                            <p className="text-xs text-gray-500 mt-1">
                                                Toegestane formaten: JPG, PNG, GIF, WebP. Maximaal 5MB.
                                            </p>
                                            
                                            {/* Remove profile image button */}
                                            {user.profile_image_url && (
                                                <button
                                                    type="button"
                                                    onClick={() => {
                                                        if (confirm('Weet je zeker dat je je profielfoto wilt verwijderen?')) {
                                                            setData('profile_image', 'remove' as any);
                                                        }
                                                    }}
                                                    className="mt-2 text-sm text-red-600 hover:text-red-800"
                                                >
                                                    Profielfoto verwijderen
                                                </button>
                                            )}
                                        </div>
                                    </div>
                                </div>

                                {/* Name Fields */}
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-2">
                                            Voornaam
                                        </label>
                                        <input
                                            type="text"
                                            value={data.first_name}
                                            onChange={(e) => setData('first_name', e.target.value)}
                                            className={`w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 ${
                                                errors.first_name ? 'border-red-500' : 'border-gray-300'
                                            }`}
                                            placeholder="Je voornaam"
                                        />
                                        {errors.first_name && (
                                            <p className="text-red-500 text-sm mt-1">{errors.first_name}</p>
                                        )}
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-2">
                                            Achternaam
                                        </label>
                                        <input
                                            type="text"
                                            value={data.last_name}
                                            onChange={(e) => setData('last_name', e.target.value)}
                                            className={`w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 ${
                                                errors.last_name ? 'border-red-500' : 'border-gray-300'
                                            }`}
                                            placeholder="Je achternaam"
                                        />
                                        {errors.last_name && (
                                            <p className="text-red-500 text-sm mt-1">{errors.last_name}</p>
                                        )}
                                    </div>
                                </div>

                                {/* Contact Information */}
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">
                                        Telefoonnummer
                                    </label>
                                    <input
                                        type="tel"
                                        value={data.phone}
                                        onChange={(e) => setData('phone', e.target.value)}
                                        className={`w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 ${
                                            errors.phone ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                        placeholder="06-12345678"
                                    />
                                    {errors.phone && (
                                        <p className="text-red-500 text-sm mt-1">{errors.phone}</p>
                                    )}
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">
                                        Geboortedatum
                                    </label>
                                    <input
                                        type="date"
                                        value={data.date_of_birth}
                                        onChange={(e) => setData('date_of_birth', e.target.value)}
                                        className={`w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 ${
                                            errors.date_of_birth ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                    />
                                    {errors.date_of_birth && (
                                        <p className="text-red-500 text-sm mt-1">{errors.date_of_birth}</p>
                                    )}
                                </div>

                                {/* Address Fields */}
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">
                                        Adres
                                    </label>
                                    <input
                                        type="text"
                                        value={data.address}
                                        onChange={(e) => setData('address', e.target.value)}
                                        className={`w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 ${
                                            errors.address ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                        placeholder="Straatnaam 123"
                                    />
                                    {errors.address && (
                                        <p className="text-red-500 text-sm mt-1">{errors.address}</p>
                                    )}
                                </div>

                                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-2">
                                            Postcode
                                        </label>
                                        <input
                                            type="text"
                                            value={data.postal_code}
                                            onChange={(e) => setData('postal_code', e.target.value)}
                                            className={`w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 ${
                                                errors.postal_code ? 'border-red-500' : 'border-gray-300'
                                            }`}
                                            placeholder="1234 AB"
                                        />
                                        {errors.postal_code && (
                                            <p className="text-red-500 text-sm mt-1">{errors.postal_code}</p>
                                        )}
                                    </div>
                                    <div className="md:col-span-2">
                                        <label className="block text-sm font-medium text-gray-700 mb-2">
                                            Plaats
                                        </label>
                                        <input
                                            type="text"
                                            value={data.city}
                                            onChange={(e) => setData('city', e.target.value)}
                                            className={`w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 ${
                                                errors.city ? 'border-red-500' : 'border-gray-300'
                                            }`}
                                            placeholder="Amsterdam"
                                        />
                                        {errors.city && (
                                            <p className="text-red-500 text-sm mt-1">{errors.city}</p>
                                        )}
                                    </div>
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">
                                        Land
                                    </label>
                                    <input
                                        type="text"
                                        value={data.country}
                                        onChange={(e) => setData('country', e.target.value)}
                                        className={`w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 ${
                                            errors.country ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                        placeholder="Nederland"
                                    />
                                    {errors.country && (
                                        <p className="text-red-500 text-sm mt-1">{errors.country}</p>
                                    )}
                                </div>

                                {/* Shooting Preferences */}
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">
                                        Voorkeur discipline
                                    </label>
                                    <select
                                        value={data.preferred_discipline}
                                        onChange={(e) => setData('preferred_discipline', e.target.value)}
                                        className={`w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 ${
                                            errors.preferred_discipline ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                    >
                                        <option value="">Geen voorkeur</option>
                                        <option value="gkp">GKP (Grote Kaliber Pistool)</option>
                                        <option value="kkp">KKP (Kleine Kaliber Pistool)</option>
                                        <option value="gkg">GKG (Grote Kaliber Geweer)</option>
                                        <option value="kkg">KKP (Kleine Kaliber Geweer)</option>
                                        <option value="luchtpistool">Luchtpistool</option>
                                        <option value="luchtgeweer">Luchtpistool</option>
                                    </select>
                                    {errors.preferred_discipline && (
                                        <p className="text-red-500 text-sm mt-1">{errors.preferred_discipline}</p>
                                    )}
                                </div>

                                {/* Privacy Settings */}
                                <div className="border-t pt-6">
                                    <h3 className="text-lg font-medium text-gray-900 mb-4">Privacy Instellingen</h3>
                                    <div className="space-y-4">
                                        <div className="flex items-center">
                                            <input
                                                type="checkbox"
                                                id="show_contact_info"
                                                checked={data.show_contact_info}
                                                onChange={(e) => setData('show_contact_info', e.target.checked)}
                                                className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                            />
                                            <label htmlFor="show_contact_info" className="ml-2 block text-sm text-gray-900">
                                                Toon mijn contactgegevens op de verenigingspagina
                                            </label>
                                        </div>
                                        <div className="flex items-center">
                                            <input
                                                type="checkbox"
                                                id="show_scores_public"
                                                checked={data.show_scores_public}
                                                onChange={(e) => setData('show_scores_public', e.target.checked)}
                                                className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                            />
                                            <label htmlFor="show_scores_public" className="ml-2 block text-sm text-gray-900">
                                                Maak mijn wedstrijdscores openbaar zichtbaar
                                            </label>
                                        </div>
                                        <div className="flex items-center">
                                            <input
                                                type="checkbox"
                                                id="show_in_participants"
                                                checked={data.show_in_participants}
                                                onChange={(e) => setData('show_in_participants', e.target.checked)}
                                                className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                            />
                                            <label htmlFor="show_in_participants" className="ml-2 block text-sm text-gray-900">
                                                Toon mijn naam in deelnemerslijsten van wedstrijden
                                            </label>
                                        </div>
                                        <div className="flex items-center">
                                            <input
                                                type="checkbox"
                                                id="show_full_name"
                                                checked={data.show_full_name}
                                                onChange={(e) => setData('show_full_name', e.target.checked)}
                                                className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                            />
                                            <label htmlFor="show_full_name" className="ml-2 block text-sm text-gray-900">
                                                Toon mijn volledige naam (anders AVG naam: J. de Vries)
                                            </label>
                                        </div>
                                        <div className="flex items-center">
                                            <input
                                                type="checkbox"
                                                id="show_contact_on_members_page"
                                                checked={data.show_contact_on_members_page}
                                                onChange={(e) => setData('show_contact_on_members_page', e.target.checked)}
                                                className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                            />
                                            <label htmlFor="show_contact_on_members_page" className="ml-2 block text-sm text-gray-900">
                                                Toon mijn gegevens op de ledenpagina
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                {/* Submit Button */}
                                <div className="pt-6">
                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white px-6 py-3 rounded-lg font-medium transition-colors"
                                    >
                                        {processing ? 'Opslaan...' : 'Wijzigingen Opslaan'}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
