import React from 'react';
import { Head, usePage } from '@inertiajs/react';
import Header from '@/components/header';
import { type SharedData } from '@/types';

export default function TestUrgent() {
    const { urgentArticles } = usePage<SharedData>().props;
    
    return (
        <div className="min-h-screen bg-gray-50">
            <Head title="Test Urgent Banner" />
            
            <Header />
            
            <div className="max-w-4xl mx-auto py-8 px-4">
                <h1 className="text-3xl font-bold mb-6">Test Urgent Banner</h1>
                
                <div className="bg-gray-100 p-4 rounded-lg mb-6">
                    <h2 className="text-xl font-semibold mb-2">Debug Info</h2>
                    <p><strong>Urgent Articles Count:</strong> {urgentArticles?.length || 0}</p>
                    <p><strong>Has Urgent Articles:</strong> {urgentArticles && Array.isArray(urgentArticles) && urgentArticles.length > 0 ? 'Yes' : 'No'}</p>
                    
                    {urgentArticles && urgentArticles.length > 0 && (
                        <div className="mt-4">
                            <h3 className="font-semibold">Urgent Articles:</h3>
                            <ul className="list-disc list-inside">
                                {urgentArticles.map(article => (
                                    <li key={article.id}>
                                        {article.title} (ID: {article.id})
                                    </li>
                                ))}
                            </ul>
                        </div>
                    )}
                </div>
                
                <div className="space-y-4">
                    <p>This page is used to test the urgent banner functionality.</p>
                    <p>If there are urgent articles, you should see a red banner at the top of the page.</p>
                    <p>When you dismiss the banner, the header should move up smoothly.</p>
                    <p>When you refresh the page, the header should remain in the correct position (moved up) if the banner was dismissed.</p>
                    
                    <div className="bg-blue-100 p-4 rounded-lg">
                        <h3 className="font-semibold text-blue-800">Instructions for Testing:</h3>
                        <ol className="list-decimal list-inside text-blue-700 space-y-1">
                            <li>Check if the urgent banner appears at the top</li>
                            <li>Click the X button to dismiss the banner</li>
                            <li>Observe if the header moves up smoothly</li>
                            <li>Refresh the page</li>
                            <li>Verify the header stays in the moved-up position</li>
                            <li>Clear localStorage to reset and test again</li>
                        </ol>
                    </div>
                    
                    <button 
                        onClick={() => {
                            localStorage.removeItem('dismissedUrgentArticles');
                            window.location.reload();
                        }}
                        className="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 mr-2"
                    >
                        Clear localStorage & Reload
                    </button>
                    
                    <button 
                        onClick={() => {
                            localStorage.setItem('debug_panel_enabled', 'true');
                            window.location.reload();
                        }}
                        className="bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600"
                    >
                        Enable Debug Panel
                    </button>
                </div>
            </div>
        </div>
    );
}
