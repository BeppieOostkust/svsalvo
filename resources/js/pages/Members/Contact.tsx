import React, { useState } from 'react';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';

interface Member {
    id: number;
    name: string;
    email: string | null;
    phone: string | null;
    disciplines: string[];
    bio: string | null;
    profile_photo: string;
}

interface PageProps {
    members: Member[];
    auth: {
        user: {
            show_contact_info: boolean;
            show_scores_public: boolean;
            show_in_participants: boolean;
            phone: string;
            disciplines: string[];
            bio: string;
        };
    };
}

export default function Contact() {
    const { members, auth } = usePage<PageProps>().props;
    const [showPrivacySettings, setShowPrivacySettings] = useState(false);

    const { data, setData, post, processing } = useForm({
        show_contact_info: auth.user.show_contact_info,
        show_scores_public: auth.user.show_scores_public,
        show_in_participants: auth.user.show_in_participants,
    });

    const { data: profileData, setData: setProfileData, post: postProfile, processing: processingProfile } = useForm({
        phone: auth.user.phone || '',
        disciplines: auth.user.disciplines || [],
        bio: auth.user.bio || '',
    });

    const handlePrivacySubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/leden/privacy-settings');
    };

    const handleProfileSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        postProfile('/leden/update-profile');
    };

    return (
        <AppLayout>
            <Head title="Ledencontact" />

            <div className="w-[90%] mx-auto px-4 py-8">
                <div className="flex justify-between items-center mb-8">
                    <h1 className="text-3xl font-bold text-gray-900">Ledencontact</h1>
                    <button
                        onClick={() => setShowPrivacySettings(!showPrivacySettings)}
                        className="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors"
                    >
                        Privacy Instellingen
                    </button>
                </div>

                {/* Privacy Settings Modal */}
                {showPrivacySettings && (
                    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                        <div className="bg-white rounded-lg p-6 max-w-md w-full">
                            <h2 className="text-2xl font-bold mb-4">Privacy Instellingen</h2>
                            <form onSubmit={handlePrivacySubmit}>
                                <div className="space-y-4">
                                    <label className="flex items-center space-x-2">
                                        <input
                                            type="checkbox"
                                            checked={data.show_contact_info}
                                            onChange={e => setData('show_contact_info', e.target.checked)}
                                            className="rounded border-gray-300"
                                        />
                                        <span>Toon mijn contactgegevens</span>
                                    </label>
                                    <label className="flex items-center space-x-2">
                                        <input
                                            type="checkbox"
                                            checked={data.show_scores_public}
                                            onChange={e => setData('show_scores_public', e.target.checked)}
                                            className="rounded border-gray-300"
                                        />
                                        <span>Toon mijn scores publiekelijk</span>
                                    </label>
                                    <label className="flex items-center space-x-2">
                                        <input
                                            type="checkbox"
                                            checked={data.show_in_participants}
                                            onChange={e => setData('show_in_participants', e.target.checked)}
                                            className="rounded border-gray-300"
                                        />
                                        <span>Toon mij in deelnemerslijsten</span>
                                    </label>
                                </div>
                                <div className="mt-6 flex justify-end space-x-3">
                                    <button
                                        type="button"
                                        onClick={() => setShowPrivacySettings(false)}
                                        className="px-4 py-2 text-gray-600 hover:text-gray-800"
                                    >
                                        Annuleren
                                    </button>
                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50"
                                    >
                                        Opslaan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                )}

                {/* Profile Update Form */}
                <div className="bg-white rounded-lg shadow-md p-6 mb-8">
                    <h2 className="text-2xl font-bold mb-4">Mijn Profiel</h2>
                    <form onSubmit={handleProfileSubmit} className="space-y-4">
                        <div>
                            <label className="block text-sm font-medium text-gray-700">Telefoonnummer</label>
                            <input
                                type="tel"
                                value={profileData.phone}
                                onChange={e => setProfileData('phone', e.target.value)}
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700">Disciplines</label>
                            <select
                                multiple
                                value={profileData.disciplines}
                                onChange={e => setProfileData('disciplines', Array.from(e.target.selectedOptions, option => option.value))}
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="luchtpistool">Luchtpistool</option>
                                <option value="luchtgeweer">Luchtgeweer</option>
                                <option value="kleinkaliberpistool">Klein Kaliber Pistool</option>
                                <option value="kleinkalibergeweer">Klein Kaliber Geweer</option>
                                <option value="grootkaliberpistool">Groot Kaliber Pistool</option>
                            </select>
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700">Bio</label>
                            <textarea
                                value={profileData.bio}
                                onChange={e => setProfileData('bio', e.target.value)}
                                rows={4}
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            />
                        </div>
                        <div className="flex justify-end">
                            <button
                                type="submit"
                                disabled={processingProfile}
                                className="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50"
                            >
                                Profiel Bijwerken
                            </button>
                        </div>
                    </form>
                </div>

                {/* Members List */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {members.map((member) => (
                        <div key={member.id} className="bg-white rounded-lg shadow-md p-6">
                            <div className="flex items-center space-x-4 mb-4">
                                <img
                                    src={member.profile_photo}
                                    alt={member.name}
                                    className="w-16 h-16 rounded-full object-cover"
                                />
                                <div>
                                    <h3 className="text-lg font-semibold">{member.name}</h3>
                                    {member.disciplines && member.disciplines.length > 0 && (
                                        <p className="text-sm text-gray-600">
                                            {member.disciplines.join(', ')}
                                        </p>
                                    )}
                                </div>
                            </div>
                            
                            {member.bio && (
                                <p className="text-gray-600 text-sm mb-4">{member.bio}</p>
                            )}
                            
                            <div className="space-y-2">
                                {member.email && (
                                    <div className="flex items-center space-x-2 text-sm">
                                        <svg className="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        <a href={`mailto:${member.email}`} className="text-blue-600 hover:underline">
                                            {member.email}
                                        </a>
                                    </div>
                                )}
                                
                                {member.phone && (
                                    <div className="flex items-center space-x-2 text-sm">
                                        <svg className="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                        <a href={`tel:${member.phone}`} className="text-blue-600 hover:underline">
                                            {member.phone}
                                        </a>
                                    </div>
                                )}
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </AppLayout>
    );
} 