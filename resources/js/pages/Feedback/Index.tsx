import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Input } from '@/components/ui/input';
import Layout from '@/components/Layout';
import { ChevronUpIcon, ChevronDownIcon, MessageCircleIcon, PlusIcon, FilterIcon } from 'lucide-react';

interface User {
    id: number;
    name: string;
    profile_image?: string;
}

interface Feedback {
    id: number;
    title: string;
    description: string;
    type: string;
    status: string;
    priority: string;
    upvotes: number;
    downvotes: number;
    is_featured: boolean;
    is_anonymous: boolean;
    created_at: string;
    user: User;
    moderator?: User;
    comments_count?: number;
}

interface Props {
    feedback: {
        data: Feedback[];
        links: any[];
        meta: any;
    };
    filters: {
        type?: string;
        status?: string;
        sort?: string;
    };
    stats: {
        total: number;
        pending: number;
        approved: number;
        implemented: number;
    };
}

const typeLabels = {
    idea: 'Idee',
    feedback: 'Feedback',
    suggestion: 'Suggestie',
    bug_report: 'Bug Report',
    feature_request: 'Feature Verzoek',
};

const statusLabels = {
    pending: 'In afwachting',
    under_review: 'In behandeling',
    approved: 'Goedgekeurd',
    rejected: 'Afgewezen',
    implemented: 'Geïmplementeerd',
    closed: 'Gesloten',
};

const statusColors = {
    pending: 'bg-yellow-100 text-yellow-800',
    under_review: 'bg-blue-100 text-blue-800',
    approved: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800',
    implemented: 'bg-purple-100 text-purple-800',
    closed: 'bg-gray-100 text-gray-800',
};

const typeColors = {
    idea: 'bg-blue-100 text-blue-800',
    feedback: 'bg-green-100 text-green-800',
    suggestion: 'bg-purple-100 text-purple-800',
    bug_report: 'bg-red-100 text-red-800',
    feature_request: 'bg-indigo-100 text-indigo-800',
};

export default function FeedbackIndex({ feedback, filters, stats }: Props) {
    const [showFilters, setShowFilters] = useState(false);

    const handleFilterChange = (key: string, value: string) => {
        router.get(route('feedback.index'), {
            ...filters,
            [key]: value === 'all' ? undefined : value,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const getInitials = (name: string) => {
        return name.split(' ').map(n => n[0]).join('').toUpperCase();
    };

    return (
        <Layout>
            <Head title="Feedback & Suggesties" />

            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                {/* Header */}
                <div className="mb-8">
                    <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h1 className="text-3xl sm:text-4xl font-bold text-gray-900 flex items-center gap-3">
                                💡 Feedback & Suggesties
                            </h1>
                            <p className="mt-2 text-gray-600 text-sm sm:text-base">
                                Deel je ideeën, feedback en suggesties om onze vereniging te verbeteren.
                            </p>
                        </div>
                        <Link href={route('feedback.create')}>
                            <Button className="bg-blue-600 hover:bg-blue-700">
                                <PlusIcon className="h-4 w-4 mr-2" />
                                Nieuwe feedback
                            </Button>
                        </Link>
                    </div>
                </div>

                {/* Stats */}
                <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                    <div className="bg-white rounded-xl border border-gray-200 p-4">
                        <div className="text-2xl font-bold text-gray-900">{stats.total}</div>
                        <div className="text-sm text-gray-600">Totaal</div>
                    </div>
                    <div className="bg-white rounded-xl border border-gray-200 p-4">
                        <div className="text-2xl font-bold text-yellow-600">{stats.pending}</div>
                        <div className="text-sm text-gray-600">In afwachting</div>
                    </div>
                    <div className="bg-white rounded-xl border border-gray-200 p-4">
                        <div className="text-2xl font-bold text-green-600">{stats.approved}</div>
                        <div className="text-sm text-gray-600">Goedgekeurd</div>
                    </div>
                    <div className="bg-white rounded-xl border border-gray-200 p-4">
                        <div className="text-2xl font-bold text-purple-600">{stats.implemented}</div>
                        <div className="text-sm text-gray-600">Geïmplementeerd</div>
                    </div>
                </div>

                {/* Filters */}
                <div className="mb-6">
                    <div className="flex items-center justify-between mb-4">
                        <Button
                            variant="outline"
                            onClick={() => setShowFilters(!showFilters)}
                            className="flex items-center gap-2"
                        >
                            <FilterIcon className="h-4 w-4" />
                            Filters {showFilters ? 'verbergen' : 'tonen'}
                        </Button>
                    </div>

                    {showFilters && (
                        <div className="bg-white rounded-xl border border-gray-200 p-6">
                            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">
                                        Type
                                    </label>
                                    <Select
                                        value={filters.type || 'all'}
                                        onValueChange={(value) => handleFilterChange('type', value)}
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Alle types" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="all">Alle types</SelectItem>
                                            {Object.entries(typeLabels).map(([value, label]) => (
                                                <SelectItem key={value} value={value}>
                                                    {label}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">
                                        Status
                                    </label>
                                    <Select
                                        value={filters.status || 'all'}
                                        onValueChange={(value) => handleFilterChange('status', value)}
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Alle statussen" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="all">Alle statussen</SelectItem>
                                            {Object.entries(statusLabels).map(([value, label]) => (
                                                <SelectItem key={value} value={value}>
                                                    {label}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">
                                        Sorteren
                                    </label>
                                    <Select
                                        value={filters.sort || 'newest'}
                                        onValueChange={(value) => handleFilterChange('sort', value)}
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Sorteren op" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="newest">Nieuwste eerst</SelectItem>
                                            <SelectItem value="oldest">Oudste eerst</SelectItem>
                                            <SelectItem value="votes">Meeste stemmen</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                            </div>
                        </div>
                    )}
                </div>

                {/* Feedback List */}
                <div className="space-y-4">
                    {feedback.data.length === 0 ? (
                        <div className="text-center py-16">
                            <div className="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <span className="text-4xl">💭</span>
                            </div>
                            <h3 className="text-lg font-medium text-gray-900 mb-2">Geen feedback gevonden</h3>
                            <p className="text-gray-500 mb-4">
                                Er zijn nog geen feedback items die voldoen aan je zoekcriteria.
                            </p>
                            <Link href={route('feedback.create')}>
                                <Button>Deel je eerste feedback</Button>
                            </Link>
                        </div>
                    ) : (
                        feedback.data.map((item) => (
                            <Card 
                                key={item.id} 
                                className={`transition-all duration-200 hover:shadow-lg ${
                                    item.is_featured ? 'ring-2 ring-yellow-400 bg-gradient-to-r from-yellow-50 to-amber-50' : ''
                                }`}
                            >
                                <CardContent className="p-6">
                                    <div className="flex items-start gap-4">
                                        {/* Avatar */}
                                        <Avatar className="h-10 w-10 flex-shrink-0">
                                            <AvatarImage 
                                                src={item.is_anonymous ? undefined : item.user?.profile_image} 
                                                alt={item.is_anonymous ? 'Anonymous' : item.user?.name} 
                                            />
                                            <AvatarFallback className="bg-gradient-to-br from-blue-500 to-purple-600 text-white">
                                                {item.is_anonymous ? '🎭' : getInitials(item.user?.name || 'U')}
                                            </AvatarFallback>
                                        </Avatar>

                                        {/* Content */}
                                        <div className="flex-1 min-w-0">
                                            <div className="flex items-start justify-between mb-2">
                                                <div className="flex-1">
                                                    <Link 
                                                        href={route('feedback.show', item.id)}
                                                        className="text-lg font-semibold text-gray-900 hover:text-blue-600 transition-colors"
                                                    >
                                                        {item.title}
                                                        {item.is_featured && (
                                                            <span className="ml-2 text-yellow-500">⭐</span>
                                                        )}
                                                    </Link>
                                                    <div className="flex items-center gap-2 mt-1">
                                                        <span className="text-sm text-gray-500">
                                                            door {item.is_anonymous ? 'Anoniem' : item.user?.name}
                                                        </span>
                                                        <span className="text-sm text-gray-400">•</span>
                                                        <span className="text-sm text-gray-500">
                                                            {new Date(item.created_at).toLocaleDateString('nl-NL')}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <p className="text-gray-600 mb-3 line-clamp-2">
                                                {item.description}
                                            </p>

                                            <div className="flex items-center justify-between">
                                                <div className="flex items-center gap-2">
                                                    <Badge className={typeColors[item.type as keyof typeof typeColors]}>
                                                        {typeLabels[item.type as keyof typeof typeLabels]}
                                                    </Badge>
                                                    <Badge className={statusColors[item.status as keyof typeof statusColors]}>
                                                        {statusLabels[item.status as keyof typeof statusLabels]}
                                                    </Badge>
                                                </div>

                                                <div className="flex items-center gap-4 text-sm text-gray-500">
                                                    <div className="flex items-center gap-1">
                                                        <ChevronUpIcon className="h-4 w-4 text-green-500" />
                                                        <span>{item.upvotes}</span>
                                                        <ChevronDownIcon className="h-4 w-4 text-red-500" />
                                                        <span>{item.downvotes}</span>
                                                    </div>
                                                    <div className="flex items-center gap-1">
                                                        <MessageCircleIcon className="h-4 w-4" />
                                                        <span>{item.comments_count || 0}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>
                        ))
                    )}
                </div>

                {/* Pagination */}
                {feedback.meta && feedback.meta.last_page > 1 && (
                    <div className="mt-8 flex justify-center">
                        <div className="flex gap-2">
                            {feedback.links.map((link: any, index: number) => (
                                <Button
                                    key={index}
                                    variant={link.active ? "default" : "outline"}
                                    disabled={!link.url}
                                    onClick={() => link.url && router.get(link.url)}
                                    dangerouslySetInnerHTML={{ __html: link.label }}
                                />
                            ))}
                        </div>
                    </div>
                )}
            </div>
        </Layout>
    );
}
