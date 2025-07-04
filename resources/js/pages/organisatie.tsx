import React from 'react';
import { Head, usePage } from '@inertiajs/react';
import Header from '@/components/header';
import { type SharedData, type OrganizationPageProps } from '@/types';
import Layout from '@/components/Layout';

interface OrganizationData extends SharedData, OrganizationPageProps {}

export default function Organisatie() {
    const { organizationInfo, boardMembers, facilities, contactInfo } = usePage<OrganizationData>().props;

    // Debug logging
    console.log('Organization data:', { organizationInfo, boardMembers, facilities, contactInfo });

    // Helper function to get icon path for facilities
    const getIconPath = (iconType: string): string => {
        const icons: Record<string, string> = {
            'building': 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
            'book': 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C20.832 18.477 19.246 18 17.5 18c-1.746 0-3.332.477-4.5 1.253',
            'users': 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
            'target': 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z',
            'shield': 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z'
        };
        return icons[iconType] || icons['building'];
    };

    // Helper function to get color classes
    const getColorClasses = (color: string): string => {
        const colors: Record<string, string> = {
            'blue': 'bg-blue-100 text-blue-600',
            'green': 'bg-green-100 text-green-600',
            'purple': 'bg-purple-100 text-purple-600',
            'red': 'bg-red-100 text-red-600',
            'yellow': 'bg-yellow-100 text-yellow-600',
            'indigo': 'bg-indigo-100 text-indigo-600',
        };
        return colors[color] || colors['blue'];
    };
    return (
        <Layout>
            <Head title="Organisatie" />
            
            
            <div className="w-[90%] mx-auto px-4 py-8">
                {/* Header */}
                <div className="mb-8">
                    <h1 className="text-4xl font-bold text-gray-900 mb-4">Organisatie</h1>
                    <p className="text-xl text-gray-600">
                        Leer meer over onze schietvereniging, onze missie en het bestuur.
                    </p>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-12">
                    {/* About Section */}
                    <div>
                        <h2 className="text-2xl font-bold text-gray-900 mb-6">Over Ons</h2>
                        
                        <div className="space-y-6">
                            {/* Mission */}
                            {organizationInfo.mission && organizationInfo.mission.map((info) => (
                                <div key={info.id}>
                                    <h3 className="text-lg font-semibold text-gray-900 mb-3">{info.title}</h3>
                                    <p className="text-gray-700">{info.content}</p>
                                </div>
                            ))}
                            
                            {/* Vision */}
                            {organizationInfo.vision && organizationInfo.vision.map((info) => (
                                <div key={info.id}>
                                    <h3 className="text-lg font-semibold text-gray-900 mb-3">{info.title}</h3>
                                    <p className="text-gray-700">{info.content}</p>
                                </div>
                            ))}
                            
                            {/* History */}
                            {organizationInfo.history && organizationInfo.history.map((info) => (
                                <div key={info.id}>
                                    <h3 className="text-lg font-semibold text-gray-900 mb-3">{info.title}</h3>
                                    <p className="text-gray-700">{info.content}</p>
                                </div>
                            ))}

                            {/* Any other organization info sections */}
                            {Object.entries(organizationInfo).map(([section, infos]) => {
                                if (['mission', 'vision', 'history'].includes(section)) return null;
                                return infos.map((info) => (
                                    <div key={info.id}>
                                        <h3 className="text-lg font-semibold text-gray-900 mb-3">{info.title}</h3>
                                        <p className="text-gray-700">{info.content}</p>
                                    </div>
                                ));
                            })}
                        </div>
                    </div>

                    {/* Board Section */}
                    <div>
                        <h2 className="text-2xl font-bold text-gray-900 mb-6">Bestuur</h2>
                        
                        <div className="space-y-4">
                            {boardMembers.map((member) => (
                                <div key={member.id} className="bg-white rounded-lg shadow-md p-6">
                                    <div className="flex items-center space-x-4">
                                        <div className="w-16 h-16 bg-gray-300 rounded-full flex items-center justify-center overflow-hidden">
                                            {member.avatar_url ? (
                                                <img 
                                                    src={member.avatar_url} 
                                                    alt={member.name}
                                                    className="w-full h-full object-cover"
                                                />
                                            ) : (
                                                <svg className="w-8 h-8 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fillRule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clipRule="evenodd" />
                                                </svg>
                                            )}
                                        </div>
                                        <div>
                                            <h3 className="text-lg font-semibold text-gray-900">{member.name}</h3>
                                            <p className="text-green-600 font-medium">{member.position}</p>
                                            {member.email && (
                                                <p className="text-sm text-gray-600">{member.email}</p>
                                            )}
                                            {member.phone && (
                                                <p className="text-sm text-gray-600">{member.phone}</p>
                                            )}
                                            {member.description && (
                                                <p className="text-sm text-gray-600 mt-2">{member.description}</p>
                                            )}
                                        </div>
                                    </div>
                                </div>
                            ))}
                            
                            {boardMembers.length === 0 && (
                                <div className="bg-white rounded-lg shadow-md p-6 text-center text-gray-500">
                                    Geen bestuursleden gevonden.
                                </div>
                            )}
                        </div>
                    </div>
                </div>

                {/* Facilities Section */}
                <div className="mt-12">
                    <h2 className="text-2xl font-bold text-gray-900 mb-6">Faciliteiten</h2>
                    
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        {facilities.map((facility) => (
                            <div key={facility.id} className="bg-white rounded-lg shadow-md p-6">
                                <div className="flex items-center mb-4">
                                    <div className={`w-12 h-12 ${getColorClasses(facility.icon_color)} rounded-lg flex items-center justify-center mr-3`}>
                                        <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d={getIconPath(facility.icon_type)} />
                                        </svg>
                                    </div>
                                    <h3 className="text-lg font-semibold text-gray-900">{facility.name}</h3>
                                </div>
                                <p className="text-gray-600">{facility.description}</p>
                                {facility.image && (
                                    <div className="mt-4">
                                        <img 
                                            src={facility.image} 
                                            alt={facility.name}
                                            className="w-full h-32 object-cover rounded-lg"
                                        />
                                    </div>
                                )}
                            </div>
                        ))}
                        
                        {facilities.length === 0 && (
                            <div className="col-span-full bg-white rounded-lg shadow-md p-6 text-center text-gray-500">
                                Geen faciliteiten gevonden.
                            </div>
                        )}
                    </div>
                </div>

                {/* Contact Information */}
                <div className="mt-12">
                    <h2 className="text-2xl font-bold text-gray-900 mb-6">Contact & Locatie</h2>
                    
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        {/* Contact Details */}
                        <div className="bg-white rounded-lg shadow-md p-6">
                            <h3 className="text-lg font-semibold text-gray-900 mb-4">
                                {contactInfo.main?.title || 'Contactgegevens'}
                            </h3>
                            <div className="space-y-3">
                                {contactInfo.main?.data?.email && (
                                    <div className="flex items-center">
                                        <svg className="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        <a href={`mailto:${contactInfo.main.data.email}`} className="text-blue-600 hover:text-blue-800 transition-colors">
                                            {contactInfo.main.data.email}
                                        </a>
                                    </div>
                                )}
                                {contactInfo.main?.data?.phone && (
                                    <div className="flex items-center">
                                        <svg className="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                        <a href={`tel:${contactInfo.main.data.phone}`} className="text-blue-600 hover:text-blue-800 transition-colors">
                                            {contactInfo.main.data.phone}
                                        </a>
                                    </div>
                                )}

                                {/* Fallback contact info if no data is available */}
                                {!contactInfo.main?.data?.email && !contactInfo.main?.data?.phone && (
                                    <div className="space-y-3">
                                        <div className="flex items-center">
                                            <svg className="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                            <a href="mailto:info@ssvdemoes.nl" className="text-blue-600 hover:text-blue-800 transition-colors">
                                                info@ssvdemoes.nl
                                            </a>
                                        </div>
                                    </div>
                                )}
                            </div>
                            
                            {/* Opening Hours */}
                            {contactInfo.hours && (
                                <div className="mt-6">
                                    <h4 className="text-md font-semibold text-gray-900 mb-3">
                                        {contactInfo.hours.title || 'Openingstijden'}
                                    </h4>
                                    <div className="space-y-2 text-sm">
                                        {contactInfo.hours.data?.hours && Array.isArray(contactInfo.hours.data.hours) ? 
                                            contactInfo.hours.data.hours.map((schedule: {day: string; hours: string}, index: number) => (
                                                <div key={index} className="flex justify-between">
                                                    <span className="font-medium">{schedule.day}</span>
                                                    <span>{schedule.hours}</span>
                                                </div>
                                            ))
                                        : contactInfo.hours.data && typeof contactInfo.hours.data === 'object' && 
                                            Object.entries(contactInfo.hours.data).map(([day, hours]: [string, any]) => (
                                                <div key={day} className="flex justify-between">
                                                    <span className="font-medium">{day}</span>
                                                    <span>{hours}</span>
                                                </div>
                                            ))
                                        }
                                    </div>
                                </div>
                            )}
                        </div>

                        {/* Address & Map */}
                        <div className="bg-white rounded-lg shadow-md p-6">
                            <h3 className="text-lg font-semibold text-gray-900 mb-4">
                                Bezoekadres
                            </h3>
                            
                            {/* Address */}
                            <div className="mb-6">
                                {contactInfo.address?.data ? (
                                    <div className="flex items-start">
                                        <svg className="w-5 h-5 text-gray-400 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <div>
                                            {contactInfo.address.data.street && <div className="font-medium">{contactInfo.address.data.street}</div>}
                                            {contactInfo.address.data.postal_code && contactInfo.address.data.city && (
                                                <div className="text-gray-600">{contactInfo.address.data.postal_code} {contactInfo.address.data.city}</div>
                                            )}
                                            {contactInfo.address.data.country && <div className="text-gray-600">{contactInfo.address.data.country}</div>}
                                        </div>
                                    </div>
                                ) : (
                                    <div className="flex items-start">
                                        <svg className="w-5 h-5 text-gray-400 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <div>
                                            <div className="font-medium">De Schacht 5</div>
                                            <div className="text-gray-600">5107 RD Dongen</div>
                                            <div className="text-gray-600">Nederland</div>
                                        </div>
                                    </div>
                                )}
                            </div>
                            
                            {/* Google Maps */}
                            <div className="space-y-3">
                                {/* Google Maps Embed */}
                                <div className="w-full h-64 bg-gray-200 rounded-lg overflow-hidden">
                                    <iframe
                                        src={`https://www.google.com/maps?q=${encodeURIComponent(
                                            'De Schacht 5, 5107 RD Dongen, Nederland'
                                        )}&output=embed`}
                                        width="100%"
                                        height="100%"
                                        style={{ border: 0 }}
                                        allowFullScreen
                                        loading="lazy"
                                        referrerPolicy="no-referrer-when-downgrade"
                                        title="Locatie op Google Maps"
                                    ></iframe>
                                </div>
                                
                                {/* Google Maps Link */}
                                <div className="flex justify-center">
                                    <a
                                        href="https://maps.app.goo.gl/SGMjk71W4rH9JT7g7"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                                    >
                                        <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                        Bekijk in Google Maps
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
