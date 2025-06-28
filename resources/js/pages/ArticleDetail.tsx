import React from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import Header from '@/components/header';
import { format } from 'date-fns';
import { nl } from 'date-fns/locale';

interface ArticleComment {
    id: number;
    content: string;
    created_at: string;
    user: {
        id: number;
        name: string;
    };
}

interface Article {
    id: number;
    title: string;
    slug: string;
    excerpt: string;
    content: string;
    featured_image: string | null;
    published_at: string;
    featured: boolean;
    author: {
        id: number;
        name: string;
    };
    comments: ArticleComment[];
}

interface PageProps {
    article: Article;
    relatedArticles: Article[];
    [key: string]: any;
}

export default function ArticleDetail() {
    const { article, relatedArticles } = usePage<PageProps>().props;

    const formatDate = (dateString: string) => {
        try {
            return format(new Date(dateString), 'd MMMM yyyy', { locale: nl });
        } catch {
            return dateString;
        }
    };

    const formatDateTime = (dateString: string) => {
        try {
            return format(new Date(dateString), 'd MMMM yyyy \om HH:mm', { locale: nl });
        } catch {
            return dateString;
        }
    };

    return (
        <>
            <Head title={article.title} />
            <Header />
            
            <div className="w-[90%] mx-auto px-4 py-8">
                {/* Breadcrumb */}
                <nav className="mb-8">
                    <ol className="flex items-center space-x-2 text-sm text-gray-500">
                        <li>
                            <Link href="/nieuws" className="hover:text-blue-600">
                                Nieuws
                            </Link>
                        </li>
                        <li>
                            <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fillRule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clipRule="evenodd" />
                            </svg>
                        </li>
                        <li className="text-gray-900 line-clamp-1">{article.title}</li>
                    </ol>
                </nav>

                <div className="grid grid-cols-1 lg:grid-cols-4 gap-8">
                    {/* Main Content */}
                    <div className="lg:col-span-3">
                        {/* Hero Image */}
                        {article.featured_image && (
                            <div className="mb-8">
                                <img
                                    src={article.featured_image}
                                    alt={article.title}
                                    className="w-full h-96 object-cover rounded-lg"
                                />
                            </div>
                        )}

                        {/* Article Header */}
                        <div className="mb-8">
                            {article.featured && (
                                <span className="bg-yellow-100 text-yellow-800 text-sm font-medium px-3 py-1 rounded-full mb-4 inline-block">
                                    Uitgelicht artikel
                                </span>
                            )}
                            
                            <h1 className="text-4xl font-bold text-gray-900 mb-4">{article.title}</h1>
                            
                            <div className="flex items-center space-x-4 text-gray-600">
                                <div className="flex items-center">
                                    <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Door {article.author.name}
                                </div>
                                <div className="flex items-center">
                                    <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {formatDate(article.published_at)}
                                </div>
                                {article.comments && article.comments.length > 0 && (
                                    <div className="flex items-center">
                                        <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                        {article.comments.length} reactie{article.comments.length !== 1 ? 's' : ''}
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* Excerpt */}
                        {article.excerpt && (
                            <div className="mb-8">
                                <p className="text-xl text-gray-700 leading-relaxed italic border-l-4 border-blue-500 pl-6">
                                    {article.excerpt}
                                </p>
                            </div>
                        )}

                        {/* Article Content */}
                        <div className="mb-12">
                            <div className="prose max-w-none prose-lg">
                                <div 
                                    className="text-gray-800 leading-relaxed"
                                    dangerouslySetInnerHTML={{ __html: article.content }}
                                />
                            </div>
                        </div>

                        {/* Comments Section */}
                        {article.comments && article.comments.length > 0 && (
                            <div className="mb-8">
                                <h2 className="text-2xl font-bold text-gray-900 mb-6">
                                    Reacties ({article.comments.length})
                                </h2>
                                
                                <div className="space-y-6">
                                    {article.comments.map((comment) => (
                                        <div key={comment.id} className="bg-gray-50 rounded-lg p-6">
                                            <div className="flex items-center justify-between mb-3">
                                                <div className="flex items-center space-x-3">
                                                    <div className="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                                                        <svg className="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fillRule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clipRule="evenodd" />
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <h3 className="font-semibold text-gray-900">{comment.user.name}</h3>
                                                    </div>
                                                </div>
                                                <span className="text-sm text-gray-500">
                                                    {formatDateTime(comment.created_at)}
                                                </span>
                                            </div>
                                            <p className="text-gray-700 whitespace-pre-line">{comment.content}</p>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        )}

                        {/* Back to News */}
                        <div className="border-t pt-8">
                            <Link 
                                href="/nieuws"
                                className="inline-flex items-center text-blue-600 hover:text-blue-700 transition-colors"
                            >
                                <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                                </svg>
                                Terug naar nieuws
                            </Link>
                        </div>
                    </div>

                    {/* Sidebar */}
                    <div className="lg:col-span-1">
                        {/* Related Articles */}
                        {relatedArticles.length > 0 && (
                            <div className="bg-white rounded-lg shadow-md p-6 sticky top-4">
                                <h3 className="text-lg font-semibold text-gray-900 mb-4">Gerelateerde Artikelen</h3>
                                
                                <div className="space-y-4">
                                    {relatedArticles.map((relatedArticle) => (
                                        <div key={relatedArticle.id} className="border-b border-gray-200 last:border-b-0 pb-4 last:pb-0">
                                            {relatedArticle.featured_image && (
                                                <img
                                                    src={relatedArticle.featured_image}
                                                    alt={relatedArticle.title}
                                                    className="w-full h-32 object-cover rounded-lg mb-3"
                                                />
                                            )}
                                            
                                            <h4 className="font-medium text-gray-900 mb-2 leading-tight">
                                                <Link
                                                    href={`/nieuws/${relatedArticle.slug}`}
                                                    className="hover:text-blue-600 transition-colors"
                                                >
                                                    {relatedArticle.title}
                                                </Link>
                                            </h4>
                                            
                                            <p className="text-sm text-gray-600 mb-2 line-clamp-2">
                                                {relatedArticle.excerpt}
                                            </p>
                                            
                                            <div className="text-xs text-gray-500">
                                                {formatDate(relatedArticle.published_at)}
                                            </div>
                                        </div>
                                    ))}
                                </div>
                                
                                <div className="mt-6 pt-4 border-t">
                                    <Link
                                        href="/nieuws"
                                        className="text-blue-600 hover:text-blue-700 text-sm font-medium transition-colors"
                                    >
                                        Bekijk alle artikelen →
                                    </Link>
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </>
    );
}
