import React, { useState, useEffect } from 'react';
import { Link } from '@inertiajs/react';

interface UrgentArticle {
    id: number;
    title: string;
    excerpt: string;
    slug: string;
    published_at: string;
    author: {
        name: string;
    };
}

interface UrgentArticlesProps {
    urgentArticles: UrgentArticle[];
}

export default function UrgentArticles({ urgentArticles }: UrgentArticlesProps) {
    try {
        const [isVisible, setIsVisible] = useState(true);
        const [dismissedArticleIds, setDismissedArticleIds] = useState<number[]>([]);

        // Safety checks
        if (!urgentArticles || !Array.isArray(urgentArticles)) {
            console.log('UrgentArticles: No articles or not array');
            return null;
        }

        console.log('UrgentArticles received:', urgentArticles);

    // Load dismissed articles from localStorage on mount
    useEffect(() => {
        const dismissed = localStorage.getItem('dismissedUrgentArticles');
        if (dismissed) {
            setDismissedArticleIds(JSON.parse(dismissed));
            console.log('Loaded dismissed articles:', JSON.parse(dismissed));
        }
    }, []);

    // Filter out dismissed articles
    const visibleArticles = urgentArticles.filter(article => 
        !dismissedArticleIds.includes(article.id)
    );

    console.log('Visible articles after filtering:', visibleArticles);
    console.log('Is visible state:', isVisible);

    // Don't show if no articles or all are dismissed or user manually dismissed
    if (!isVisible || !visibleArticles || visibleArticles.length === 0) {
        console.log('Not showing banner because:', { isVisible, visibleArticlesLength: visibleArticles?.length });
        return null;
    }

    const handleDismiss = () => {
        console.log('Dismiss button clicked');
        // Add current visible article IDs to dismissed list
        const newDismissedIds = [...dismissedArticleIds, ...visibleArticles.map(a => a.id)];
        setDismissedArticleIds(newDismissedIds);
        localStorage.setItem('dismissedUrgentArticles', JSON.stringify(newDismissedIds));
        setIsVisible(false);
        
        // Dispatch custom event to notify header about banner dismissal
        console.log('Dispatching urgentBannerDismissed event');
        window.dispatchEvent(new CustomEvent('urgentBannerDismissed', { detail: { dismissed: true } }));
    };

    return (
        <div className="fixed top-0 left-0 right-0 z-50 bg-red-600 text-white shadow-lg">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div className="py-3">
                    <div className="grid grid-cols-12 gap-4 items-center">
                        {/* Icon */}
                        <div className="col-span-1">
                            <svg 
                                className="h-6 w-6 text-white animate-pulse" 
                                fill="none" 
                                stroke="currentColor" 
                                viewBox="0 0 24 24"
                            >
                                <path 
                                    strokeLinecap="round" 
                                    strokeLinejoin="round" 
                                    strokeWidth={2} 
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" 
                                />
                            </svg>
                        </div>
                        
                        {/* Content */}
                        <div className="col-span-8">
                            <p className="text-sm font-medium">
                                <span className="font-bold">URGENT:</span> 
                                {visibleArticles.length === 1 ? (
                                    <Link 
                                        href={`/nieuws/${visibleArticles[0].slug}`}
                                        className="hover:underline ml-2"
                                    >
                                        {visibleArticles[0].title}
                                    </Link>
                                ) : (
                                    <span className="ml-2">
                                        {visibleArticles.length} urgente berichten - 
                                        <Link 
                                            href="/nieuws" 
                                            className="hover:underline ml-1"
                                        >
                                            Bekijk alle berichten
                                        </Link>
                                    </span>
                                )}
                            </p>
                            {visibleArticles.length === 1 && visibleArticles[0].excerpt && (
                                <p className="text-xs opacity-90 mt-1">
                                    {visibleArticles[0].excerpt.substring(0, 60)}...
                                </p>
                            )}
                        </div>
                        
                        {/* Buttons */}
                        <div className="col-span-3 flex justify-end space-x-2">
                            <Link
                                href={visibleArticles.length === 1 ? `/nieuws/${visibleArticles[0].slug}` : "/nieuws"}
                                className="bg-black text-white hover:bg-gray-800 rounded px-3 py-1 text-xs font-medium transition-colors"
                            >
                                Lees
                            </Link>
                            <button
                                onClick={handleDismiss}
                                className="bg-black text-white hover:bg-gray-800 rounded p-1 transition-colors"
                                title="Verberg dit bericht"
                            >
                                <svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path 
                                        strokeLinecap="round" 
                                        strokeLinejoin="round" 
                                        strokeWidth={2} 
                                        d="M6 18L18 6M6 6l12 12" 
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
    } catch (error) {
        console.error('Error in UrgentArticles component:', error);
        return null;
    }
}
