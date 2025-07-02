import React from 'react';
import { Head, Link } from '@inertiajs/react';
import Header from '@/components/header';
import { format } from 'date-fns';
import Layout from '@/components/Layout';

interface Member {
    id: number;
    name: string;
    first_name: string | null;
    last_name: string | null;
    email: string | null;
    phone: string | null;
    city: string | null;
    bio: string | null;
    profile_image_url: string | null;
    position: string | null;
    member_since: string | null;
    preferred_discipline: string | null;
    show_contact_info: boolean;
    is_anonymous: boolean;
}

interface PageProps {
    boardMembers: Member[];
    regularMembers: Member[];
    totalMembers: number;
    totalActiveMembers: number;
}

export default function VereenigingIndex({ boardMembers, regularMembers, totalMembers, totalActiveMembers }: PageProps) {
    const formatDate = (dateString: string | null) => {
        if (!dateString) return null;
        try {
            return format(new Date(dateString), 'd MMMM yyyy');
        } catch {
            return dateString;
        }
    };

    const getDisciplineDisplayName = (discipline: string | null) => {
        const disciplines = {
            'gkp': 'GKP (Grote Kaliber Pistool)',
            'kkp': 'KKP (Kleine Kaliber Pistool)',
            'gkg': 'GKG (Grote Kaliber Geweer)',
            'kkg': 'KKG (Kleine Kaliber Geweer)',
            'luchtpistool': 'Luchtpistool',
            'luchtgeweer': 'Luchtgeweer',
        };
        return discipline ? disciplines[discipline as keyof typeof disciplines] || discipline : null;
    };

    const MemberCard = ({ member }: { member: Member }) => (
        <div className="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
            {/* Profile Image and Basic Info */}
            <div className="flex items-start space-x-4">
                <div className="flex-shrink-0">
                    {member.profile_image_url && !member.is_anonymous ? (
                        <img
                            src={member.profile_image_url}
                            alt={member.name}
                            className="w-16 h-16 rounded-full object-cover border-2 border-gray-200"
                        />
                    ) : (
                        <div className="w-16 h-16 rounded-full bg-gray-300 flex items-center justify-center border-2 border-gray-200">
                            <span className="text-gray-600 font-semibold text-xl">
                                {member.is_anonymous ? '?' : (member.first_name?.[0] || member.name[0]).toUpperCase()}
                            </span>
                        </div>
                    )}
                </div>
                
                <div className="flex-1 min-w-0">
                    {/* Name and Position */}
                    <div className="mb-2">
                        <h3 className="text-lg font-semibold text-gray-900 truncate">
                            {member.name}
                        </h3>
                        {member.position && (
                            <p className="text-sm font-medium text-blue-600">
                                {member.position}
                            </p>
                        )}
                    </div>

                    {/* Member Since */}
                    {member.member_since && (
                        <p className="text-sm text-gray-500 mb-2">
                            Lid sinds: {formatDate(member.member_since)}
                        </p>
                    )}

                    {/* Preferred Discipline */}
                    {member.preferred_discipline && (
                        <p className="text-sm text-gray-600 mb-3">
                            <span className="font-medium">Discipline:</span> {getDisciplineDisplayName(member.preferred_discipline)}
                        </p>
                    )}

                    {/* Bio */}
                    {member.bio && !member.is_anonymous && (
                        <p className="text-sm text-gray-700 mb-3 italic">
                            "{member.bio}"
                        </p>
                    )}

                    {/* Contact Information - Only shown if privacy setting allows */}
                    {!member.is_anonymous && (
                        <div className="space-y-1">
                            {member.email && (
                                <p className="text-sm text-gray-600">
                                    <span className="font-medium">Email:</span>{' '}
                                    <a href={`mailto:${member.email}`} className="text-blue-600 hover:text-blue-800">
                                        {member.email}
                                    </a>
                                </p>
                            )}
                            {member.phone && (
                                <p className="text-sm text-gray-600">
                                    <span className="font-medium">Telefoon:</span>{' '}
                                    <a href={`tel:${member.phone}`} className="text-blue-600 hover:text-blue-800">
                                        {member.phone}
                                    </a>
                                </p>
                            )}
                            {member.city && (
                                <p className="text-sm text-gray-600">
                                    <span className="font-medium">Plaats:</span> {member.city}
                                </p>
                            )}
                        </div>
                    )}

                    {/* Privacy Notice for Anonymous Members */}
                    {member.is_anonymous && (
                        <div className="mt-3 p-2 bg-gray-50 rounded-md">
                            <p className="text-xs text-gray-500">
                                Dit lid heeft ervoor gekozen om anoniem te blijven op de verenigingspagina.
                            </p>
                        </div>
                    )}
                </div>
            </div>
        </div>
    );

    return (
        <Layout>
            <Head title="Vereniging - Leden" />
            
            
            <div className="w-[90%] mx-auto px-4 py-8">
                {/* Page Header */}
                <div className="mb-8">
                    <h1 className="text-3xl font-bold text-gray-900 mb-4">Onze Vereniging</h1>
                    <div className="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p className="text-blue-800">
                            Welkom op onze verenigingspagina! Hier kun je kennismaken met onze leden. 
                            Leden kunnen zelf kiezen of ze hun contactgegevens willen delen via hun privacy instellingen.
                        </p>
                        <div className="mt-2 text-sm text-blue-600">
                            <span className="font-medium">Totaal aantal actieve leden:</span> {totalActiveMembers}
                        </div>
                    </div>
                </div>

                {/* Board Members Section */}
                {boardMembers.length > 0 && (
                    <div className="mb-12">
                        <div className="flex items-center justify-between mb-6">
                            <h2 className="text-2xl font-bold text-gray-900">Bestuur</h2>
                            <span className="text-sm text-gray-500">{boardMembers.length} bestuursleden</span>
                        </div>
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            {boardMembers.map((member) => (
                                <MemberCard key={member.id} member={member} />
                            ))}
                        </div>
                    </div>
                )}

                {/* Regular Members Section */}
                <div>
                    <div className="flex items-center justify-between mb-6">
                        <h2 className="text-2xl font-bold text-gray-900">Leden</h2>
                        <span className="text-sm text-gray-500">{regularMembers.length} leden</span>
                    </div>
                    
                    {regularMembers.length > 0 ? (
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            {regularMembers.map((member) => (
                                <MemberCard key={member.id} member={member} />
                            ))}
                        </div>
                    ) : (
                        <div className="text-center py-12">
                            <p className="text-gray-500">Er zijn momenteel geen leden om weer te geven.</p>
                        </div>
                    )}
                </div>

                {/* Privacy Information */}
                <div className="mt-12 bg-gray-50 border border-gray-200 rounded-lg p-6">
                    <h3 className="text-lg font-semibold text-gray-900 mb-3">Privacy Informatie</h3>
                    <div className="text-sm text-gray-700 space-y-2">
                        <p>
                            <strong>Contactgegevens:</strong> Leden kunnen ervoor kiezen om hun contactgegevens 
                            (email, telefoon, plaats) te delen op deze pagina via hun profiel instellingen.
                        </p>
                        <p>
                            <strong>Anonieme leden:</strong> Leden die hun contactgegevens niet willen delen 
                            worden weergegeven als "Anonieme Lid" zonder persoonlijke informatie.
                        </p>
                        <p>
                            Als je lid bent en je privacy instellingen wilt aanpassen, kun je dit doen via je{' '}
                            <Link href="/profile" className="text-blue-600 hover:text-blue-800 underline">
                                profiel pagina
                            </Link>.
                        </p>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
