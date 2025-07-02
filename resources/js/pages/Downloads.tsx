import React, { useState } from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import Header from '@/components/header';
import Layout from '@/components/Layout';

interface Download {
    id: number;
    title: string;
    description: string;
    file_name: string;
    file_type: string;
    file_size: number;
    category: string;
    is_public: boolean;
    requires_login: boolean;
    download_count: number;
    allowed_roles: string[];
    created_at: string;
    uploader?: {
        id: number;
        name: string;
    };
}

interface PageProps {
    downloads: Record<string, Download[]>;
    categories: Record<string, string>;
    user?: {
        id: number;
        name: string;
        email: string;
        is_admin: boolean;
        email_verified_at: string | null;
    };
    [key: string]: any;
}

export default function Downloads() {
    const { downloads, categories, user } = usePage<PageProps>().props;
    const [selectedCategory, setSelectedCategory] = useState<string | null>(null);
    const [downloadingIds, setDownloadingIds] = useState<Set<number>>(new Set());

    const formatFileSize = (bytes: number) => {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
    };

    const getFileIcon = (fileType: string) => {
        const type = fileType.toLowerCase();
        if (type.includes('pdf')) {
            return (
                <svg className="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fillRule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clipRule="evenodd" />
                </svg>
            );
        } else if (type.includes('word') || type.includes('doc')) {
            return (
                <svg className="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fillRule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clipRule="evenodd" />
                </svg>
            );
        } else if (type.includes('excel') || type.includes('sheet')) {
            return (
                <svg className="w-8 h-8 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fillRule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clipRule="evenodd" />
                </svg>
            );
        } else if (type.includes('image')) {
            return (
                <svg className="w-8 h-8 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fillRule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clipRule="evenodd" />
                </svg>
            );
        }
        return (
            <svg className="w-8 h-8 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                <path fillRule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clipRule="evenodd" />
            </svg>
        );
    };

    const getAccessLevelBadge = (download: Download) => {
        if (download.is_public) {
            return <span className="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Openbaar</span>;
        } else if (download.requires_login) {
            return <span className="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">Leden</span>;
        } else {
            return <span className="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">Beperkt</span>;
        }
    };

    const canAccessDownload = (download: Download) => {
        // If public, everyone can access
        if (download.is_public) return true;
        
        // If requires login and user is not logged in
        if (download.requires_login && !user) return false;
        
        // If user is logged in and admin, always allow
        if (user && user.is_admin) return true;
        
        // If user is logged in and it just requires login (no specific roles)
        if (user && download.requires_login && (!download.allowed_roles || download.allowed_roles.length === 0)) {
            return true;
        }
        
        return user ? true : false;
    };

    const getDownloadButton = (download: Download) => {
        if (!canAccessDownload(download)) {
            return (
                <Link
                    href="/login"
                    className="w-full bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors text-center block"
                >
                    Login vereist
                </Link>
            );
        }

        return (
            <a
                href={`/download/${download.id}`}
                className={`w-full px-4 py-2 rounded-lg transition-colors text-center block ${
                    downloadingIds.has(download.id) 
                        ? 'bg-gray-400 text-white cursor-not-allowed' 
                        : 'bg-blue-600 text-white hover:bg-blue-700'
                }`}
                target="_blank"
                rel="noopener noreferrer"
                onClick={(e) => {
                    if (downloadingIds.has(download.id)) {
                        e.preventDefault();
                        return;
                    }
                    
                    // Add visual feedback
                    setDownloadingIds(prev => new Set(prev).add(download.id));
                    console.log(`Starting download for: ${download.title}`);
                    
                    // Remove the downloading state after a short delay
                    setTimeout(() => {
                        setDownloadingIds(prev => {
                            const newSet = new Set(prev);
                            newSet.delete(download.id);
                            return newSet;
                        });
                    }, 2000);
                }}
            >
                {downloadingIds.has(download.id) ? 'Downloaden...' : 'Download'}
            </a>
        );
    };

    const filteredDownloads = selectedCategory 
        ? { [selectedCategory]: downloads[selectedCategory] || [] }
        : downloads;

    const allDownloads = Object.values(downloads).flat();

    return (
        <Layout>
            <Head title="Downloads" />
            
            
            <div className="w-[90%] mx-auto px-4 py-8">
                {/* Header */}
                <div className="mb-8">
                    <h1 className="text-4xl font-bold text-gray-900 mb-4">Downloads</h1>
                    <p className="text-xl text-gray-600">
                        Download documenten, formulieren en andere bestanden van onze schietvereniging.
                    </p>
                </div>

                {/* Category Filter */}
                <div className="mb-8">
                    <div className="flex flex-wrap gap-2">
                        <button
                            onClick={() => setSelectedCategory(null)}
                            className={`px-4 py-2 rounded-lg transition-colors ${
                                selectedCategory === null
                                    ? 'bg-blue-600 text-white'
                                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                            }`}
                        >
                            Alle categorieën
                        </button>
                        {Object.entries(categories).map(([key, label]) => (
                            downloads[key] && downloads[key].length > 0 && (
                                <button
                                    key={key}
                                    onClick={() => setSelectedCategory(key)}
                                    className={`px-4 py-2 rounded-lg transition-colors ${
                                        selectedCategory === key
                                            ? 'bg-blue-600 text-white'
                                            : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                                    }`}
                                >
                                    {label} ({downloads[key].length})
                                </button>
                            )
                        ))}
                    </div>
                </div>

                {/* Downloads by Category */}
                {Object.keys(filteredDownloads).length === 0 ? (
                    <div className="text-center py-12">
                        <div className="text-gray-400 mb-4">
                            <svg className="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 className="text-lg font-medium text-gray-900 mb-2">Geen downloads gevonden</h3>
                        <p className="text-gray-600">Er zijn momenteel geen downloads beschikbaar in deze categorie.</p>
                    </div>
                ) : (
                    <div className="space-y-8">
                        {Object.entries(filteredDownloads).map(([categoryKey, categoryDownloads]) => (
                            categoryDownloads.length > 0 && (
                                <div key={categoryKey}>
                                    <h2 className="text-2xl font-bold text-gray-900 mb-6">{categories[categoryKey]}</h2>
                                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                        {categoryDownloads.map((download) => (
                                            <div key={download.id} className="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                                                <div className="p-6">
                                                    <div className="flex items-start justify-between mb-4">
                                                        <div className="flex items-center space-x-3">
                                                            {getFileIcon(download.file_type)}
                                                            <div>
                                                                <h3 className="text-lg font-semibold text-gray-900">
                                                                    {download.title}
                                                                </h3>
                                                            </div>
                                                        </div>
                                                        {getAccessLevelBadge(download)}
                                                    </div>
                                                    
                                                    <p className="text-gray-600 mb-4 line-clamp-3">
                                                        {download.description}
                                                    </p>
                                                    
                                                    <div className="flex justify-between items-center text-sm text-gray-500 mb-4">
                                                        <span>{formatFileSize(download.file_size)}</span>
                                                        <span>{download.download_count} downloads</span>
                                                    </div>
                                                    
                                                    {getDownloadButton(download)}
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            )
                        ))}
                    </div>
                )}
            </div>
        </Layout>
    );
}
