import React from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import Header from '@/components/header';
import { format } from 'date-fns';
import { nl } from 'date-fns/locale';
import Layout from '@/components/Layout';

interface Article {
    id: number;
    title: string;
    slug: string;
    excerpt: string;
    content: string;
    featured_image: string | null;
    published_at: string;
    is_featured: boolean;
    is_urgent: boolean;
    author: {
        id: number;
        name: string;
    };
}

interface PageProps {
    articles: {
        data: Article[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        next_page_url: string | null;
        prev_page_url: string | null;
    };
    [key: string]: any;
}

export default function Nieuws() {
    const { articles } = usePage<PageProps>().props;

    const formatDate = (dateString: string) => {
        try {
            return format(new Date(dateString), 'd MMMM yyyy', { locale: nl });
        } catch {
            return dateString;
        }
    };

    const getArticleImage = (article: Article) => {
        if (article.featured_image) {
            return article.featured_image;
        }
        // Use article ID to consistently assign the same default image to the same article
        const images = [
            '/images/backgrounds/1.jpg',
            '/images/backgrounds/2.jpg',
            '/images/backgrounds/3.jpg',
            '/images/backgrounds/4.png'
        ];
        return images[article.id % images.length];
    };

    return (
        <Layout>
            <Head title="Nieuws" />
            
            
            <div className="w-[90%] mx-auto px-6 py-12">
                <div className="mb-12">
                    <h1 className="text-4xl font-bold mb-2">Nieuws</h1>
                    <p className="text-gray-600 text-lg">Blijf op de hoogte van het laatste nieuws van onze schietvereniging</p>
                </div>
                
                {articles.data.length === 0 ? (
                    <div className="py-12">
                        <p className="text-gray-600">Er zijn momenteel geen artikelen beschikbaar.</p>
                    </div>
                ) : (
                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        {articles.data.map((article) => (
                            <Link
                                key={article.id}
                                href={`/nieuws/${article.slug}`}
                                className={`bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 relative flex flex-col h-full ${
                                    article.is_urgent ? 'ring-2 ring-red-500 shadow-red-100' : ''
                                }`}
                            >

                                <div className="relative">
                                    <img
                                        src={getArticleImage(article)}
                                        alt={article.title}
                                        className="w-full h-48 object-cover"
                                    />
                                    
                                    {/* Featured Badge */}
                                    {article.is_featured && (
                                        <div className="absolute top-3 left-3 z-10">
                                            <div className="bg-yellow-400 text-yellow-900 text-xs font-bold px-2 py-1 rounded-full shadow-lg flex items-center gap-1">
                                                <span>⭐</span>
                                                <span>Featured</span>
                                            </div>
                                        </div>
                                    )}
                                    
                                    {/* Urgent Badge */}
                                    {article.is_urgent && (
                                        <div className="absolute top-3 right-3 z-10">
                                            <div className="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full shadow-lg flex items-center gap-1 animate-pulse">
                                                <span>🚨</span>
                                                <span>URGENT</span>
                                            </div>
                                        </div>
                                    )}
                                </div>
                                <div className="p-6 flex flex-col flex-grow">
                                    {/* Title and Description Section */}
                                    <div className="flex-grow">
                                        <h2 className={`text-xl font-bold mb-3 hover:text-blue-600 transition-colors line-clamp-2 ${
                                            article.is_urgent ? 'text-red-700' : 'text-gray-900'
                                        }`}>
                                            {article.title}
                                        </h2>
                                        <p className="text-gray-600 mb-6 line-clamp-3 text-sm leading-relaxed">
                                            {article.excerpt}
                                        </p>
                                    </div>
                                    
                                    {/* Author and Date Section */}
                                    <div className="border-t border-gray-100 pt-4 mt-auto">
                                        <div className="flex justify-between items-center text-sm text-gray-500">
                                            <span className="font-medium">Door {article.author.name}</span>
                                            <span>{formatDate(article.published_at)}</span>
                                        </div>
                                    </div>
                                </div>
                            </Link>
                        ))}
                    </div>
                )}

                {/* Pagination */}
                {articles.last_page > 1 && (
                    <div className="flex justify-start items-center space-x-4 mt-12">
                        {articles.prev_page_url && (
                            <Link
                                href={articles.prev_page_url}
                                preserveScroll
                                className="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                            >
                                Vorige
                            </Link>
                        )}
                        
                        <span className="px-6 py-3 text-gray-700">
                            Pagina {articles.current_page} van {articles.last_page}
                        </span>
                        
                        {articles.next_page_url && (
                            <Link
                                href={articles.next_page_url}
                                preserveScroll
                                className="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                            >
                                Volgende
                            </Link>
                        )}
                    </div>
                )}
            </div>
        </Layout>
    );
}
