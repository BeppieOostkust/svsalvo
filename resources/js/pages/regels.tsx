import React from 'react';
import { Head, usePage } from '@inertiajs/react';
import Layout from '@/components/Layout';
import { SharedData } from '@/types';

interface Rule {
    id: number;
    title: string;
    content: string;
    category: string | null;
    order: number;
    is_active: boolean;
}

interface Price {
    id: number;
    title: string;
    description: string | null;
    amount: number;
    currency: string;
    category: string | null;
    period: string | null;
    order: number;
    is_active: boolean;
    formatted_amount: string;
}

interface Props extends SharedData {
    rules: Rule[];
    prices: Price[];
}

export default function Regels() {
    const { rules, prices } = usePage<Props>().props;

    // Group rules by category
    const groupedRules = rules.reduce((acc, rule) => {
        const category = rule.category || 'Algemeen';
        if (!acc[category]) {
            acc[category] = [];
        }
        acc[category].push(rule);
        return acc;
    }, {} as Record<string, Rule[]>);

    // Group prices by category
    const groupedPrices = prices.reduce((acc, price) => {
        const category = price.category || 'Algemeen';
        if (!acc[category]) {
            acc[category] = [];
        }
        acc[category].push(price);
        return acc;
    }, {} as Record<string, Price[]>);

    return (
        <Layout>
            <Head title="Regels & Prijzen" />
            
            <div className="w-[90%] mx-auto px-4 py-8">
                {/* Header */}
                <div className="mb-8">
                    <h1 className="text-4xl font-bold text-gray-900 mb-4">Regels & Prijzen</h1>
                    <p className="text-xl text-gray-600">
                        Hier vindt u alle regels en prijsinformatie van SSV de Moes.
                    </p>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-12">
                    {/* Rules Section */}
                    <div>
                        <h2 className="text-2xl font-bold text-gray-900 mb-6">Regels</h2>
                        
                        {Object.keys(groupedRules).length > 0 ? (
                            <div className="space-y-8">
                                {Object.entries(groupedRules).map(([category, categoryRules]) => (
                                    <div key={category}>
                                        <h3 className="text-xl font-semibold text-gray-900 mb-4">
                                            {category}
                                        </h3>
                                        <div className="space-y-4">
                                            {categoryRules.map((rule) => (
                                                <div key={rule.id} className="bg-white rounded-lg shadow-md p-6">
                                                    <h4 className="text-lg font-medium text-gray-900 mb-3">
                                                        {rule.title}
                                                    </h4>
                                                    <div className="text-gray-700 whitespace-pre-wrap">
                                                        {rule.content}
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <div className="bg-white rounded-lg shadow-md p-6 text-center text-gray-500">
                                <svg className="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p>Geen regels beschikbaar.</p>
                            </div>
                        )}
                    </div>

                    {/* Prices Section */}
                    <div>
                        <h2 className="text-2xl font-bold text-gray-900 mb-6">Prijzen</h2>
                        
                        {Object.keys(groupedPrices).length > 0 ? (
                            <div className="space-y-8">
                                {Object.entries(groupedPrices).map(([category, categoryPrices]) => (
                                    <div key={category}>
                                        <h3 className="text-xl font-semibold text-gray-900 mb-4">
                                            {category}
                                        </h3>
                                        <div className="space-y-4">
                                            {categoryPrices.map((price) => (
                                                <div key={price.id} className="bg-white rounded-lg shadow-md p-6">
                                                    <div className="flex justify-between items-start mb-3">
                                                        <h4 className="text-lg font-medium text-gray-900">
                                                            {price.title}
                                                        </h4>
                                                        <div className="text-right">
                                                            <div className="text-2xl font-bold text-green-600">
                                                                € {Number(price.amount).toFixed(2).replace('.', ',')}
                                                            </div>
                                                            {price.period && (
                                                                <div className="text-sm text-gray-500">
                                                                    {price.period}
                                                                </div>
                                                            )}
                                                        </div>
                                                    </div>
                                                    {price.description && (
                                                        <p className="text-gray-700">
                                                            {price.description}
                                                        </p>
                                                    )}
                                                </div>
                                            ))}
                                        </div>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <div className="bg-white rounded-lg shadow-md p-6 text-center text-gray-500">
                                <svg className="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                </svg>
                                <p>Geen prijzen beschikbaar.</p>
                            </div>
                        )}
                    </div>
                </div>

                {/* Additional Information */}
                <div className="mt-12">
                    <div className="bg-blue-50 border border-blue-200 rounded-lg p-6">
                        <div className="flex items-start">
                            <svg className="w-6 h-6 text-blue-600 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <h3 className="text-lg font-medium text-blue-900 mb-2">
                                    Vragen over regels of prijzen?
                                </h3>
                                <p className="text-blue-800">
                                    Neem contact op met het bestuur voor meer informatie of verduidelijking van de regels en prijzen.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
