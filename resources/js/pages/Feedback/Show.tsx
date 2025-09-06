import React, { useState } from 'react';
import { Head, useForm, router } from '@inertiajs/react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Alert, AlertDescription } from '@/components/ui/alert';
import Layout from '@/components/Layout';
import { 
    ArrowLeftIcon, 
    ChevronUpIcon, 
    ChevronDownIcon, 
    MessageCircleIcon,
    CalendarIcon,
    UserIcon,
    FlagIcon,
    Trash2Icon
} from 'lucide-react';

interface User {
    id: number;
    name: string;
    profile_image?: string;
    is_admin?: boolean;
}

interface Comment {
    id: number;
    content: string;
    created_at: string;
    is_moderator_comment: boolean;
    user: User;
}

interface Vote {
    id: number;
    vote_type: 'upvote' | 'downvote';
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
    admin_response?: string;
    user: User;
    moderator?: User;
    comments: Comment[];
}

interface Props {
    feedback: Feedback;
    userVote?: Vote;
    auth: {
        user: User | null;
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

export default function FeedbackShow({ feedback, userVote, auth }: Props) {
    const [showCommentForm, setShowCommentForm] = useState(false);
    const [voteProcessing, setVoteProcessing] = useState(false);
    
    const { data: commentData, setData: setCommentData, post: postComment, processing: commentProcessing, reset: resetComment } = useForm({
        content: '',
    });

    const handleVote = (voteType: 'upvote' | 'downvote') => {
        setVoteProcessing(true);
        router.post(route('feedback.vote', feedback.id), {
            vote_type: voteType
        }, {
            preserveScroll: true,
            onFinish: () => setVoteProcessing(false),
        });
    };

    const handleComment = (e: React.FormEvent) => {
        e.preventDefault();
        postComment(route('feedback.comment', feedback.id), {
            onSuccess: () => {
                resetComment();
                setShowCommentForm(false);
            },
            preserveScroll: true,
        });
    };

    const handleDeleteComment = (commentId: number) => {
        if (confirm('Weet je zeker dat je deze reactie wilt verwijderen?')) {
            router.delete(route('feedback.comment.delete', commentId), {
                preserveScroll: true,
            });
        }
    };

    const canDeleteComment = (comment: Comment) => {
        if (!auth.user) return false;
        // User can delete their own comment or if they are admin
        return comment.user.id === auth.user.id || auth.user.is_admin;
    };

    const getInitials = (name: string) => {
        return name.split(' ').map(n => n[0]).join('').toUpperCase();
    };

    const formatDate = (dateString: string) => {
        return new Date(dateString).toLocaleDateString('nl-NL', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    };

    return (
        <Layout>
            <Head title={feedback.title} />

            <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                {/* Header */}
                <div className="mb-8">
                    <Button
                        variant="outline"
                        onClick={() => router.get(route('feedback.index'))}
                        className="flex items-center gap-2 mb-4"
                    >
                        <ArrowLeftIcon className="h-4 w-4" />
                        Terug naar overzicht
                    </Button>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {/* Main Content */}
                    <div className="lg:col-span-2 space-y-6">
                        {/* Feedback Card */}
                        <Card className={feedback.is_featured ? 'ring-2 ring-yellow-400' : ''}>
                            <CardHeader>
                                <div className="flex items-start justify-between">
                                    <div className="flex-1">
                                        <div className="flex items-center gap-2 mb-2">
                                            <Badge className={typeColors[feedback.type as keyof typeof typeColors]}>
                                                {typeLabels[feedback.type as keyof typeof typeLabels]}
                                            </Badge>
                                            <Badge className={statusColors[feedback.status as keyof typeof statusColors]}>
                                                {statusLabels[feedback.status as keyof typeof statusLabels]}
                                            </Badge>
                                            {feedback.is_featured && (
                                                <Badge variant="outline" className="text-yellow-600 border-yellow-600">
                                                    <FlagIcon className="h-3 w-3 mr-1" />
                                                    Uitgelicht
                                                </Badge>
                                            )}
                                        </div>
                                        <CardTitle className="text-2xl">
                                            {feedback.title}
                                        </CardTitle>
                                    </div>
                                </div>

                                <div className="flex items-center gap-4 text-sm text-gray-600">
                                    <div className="flex items-center gap-2">
                                        <Avatar className="h-6 w-6">
                                            <AvatarImage 
                                                src={feedback.is_anonymous ? undefined : feedback.user?.profile_image} 
                                                alt={feedback.is_anonymous ? 'Anonymous' : feedback.user?.name} 
                                            />
                                            <AvatarFallback className="text-xs">
                                                {feedback.is_anonymous ? '🎭' : getInitials(feedback.user?.name || 'U')}
                                            </AvatarFallback>
                                        </Avatar>
                                        <span>
                                            {feedback.is_anonymous ? 'Anoniem' : feedback.user?.name}
                                        </span>
                                    </div>
                                    <div className="flex items-center gap-1">
                                        <CalendarIcon className="h-4 w-4" />
                                        <span>{formatDate(feedback.created_at)}</span>
                                    </div>
                                </div>
                            </CardHeader>

                            <CardContent>
                                <div className="prose max-w-none">
                                    <p className="text-gray-700 whitespace-pre-wrap">
                                        {feedback.description}
                                    </p>
                                </div>

                                {/* Admin Response */}
                                {feedback.admin_response && (
                                    <Alert className="mt-6 border-blue-200 bg-blue-50">
                                        <UserIcon className="h-4 w-4" />
                                        <AlertDescription>
                                            <div className="font-medium text-blue-900 mb-2">Reactie van beheerder:</div>
                                            <div className="text-blue-800">{feedback.admin_response}</div>
                                            {feedback.moderator && (
                                                <div className="text-sm text-blue-600 mt-2">
                                                    — {feedback.moderator.name}
                                                </div>
                                            )}
                                        </AlertDescription>
                                    </Alert>
                                )}

                                {/* Voting */}
                                <div className="flex items-center gap-4 mt-6 pt-4 border-t">
                                    <div className="flex items-center gap-2">
                                        <Button
                                            variant={userVote?.vote_type === 'upvote' ? 'default' : 'outline'}
                                            size="sm"
                                            onClick={() => handleVote('upvote')}
                                            disabled={voteProcessing}
                                            className="flex items-center gap-1"
                                        >
                                            <ChevronUpIcon className="h-4 w-4" />
                                            {feedback.upvotes}
                                        </Button>
                                        <Button
                                            variant={userVote?.vote_type === 'downvote' ? 'default' : 'outline'}
                                            size="sm"
                                            onClick={() => handleVote('downvote')}
                                            disabled={voteProcessing}
                                            className="flex items-center gap-1"
                                        >
                                            <ChevronDownIcon className="h-4 w-4" />
                                            {feedback.downvotes}
                                        </Button>
                                    </div>
                                    <div className="text-sm text-gray-500">
                                        Net stemmen: {feedback.upvotes - feedback.downvotes}
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        {/* Comments */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <MessageCircleIcon className="h-5 w-5" />
                                    Reacties ({feedback.comments.length})
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="space-y-4">
                                    {feedback.comments.length === 0 ? (
                                        <p className="text-gray-500 text-center py-8">
                                            Nog geen reacties. Wees de eerste!
                                        </p>
                                    ) : (
                                        feedback.comments.map((comment) => (
                                            <div key={comment.id} className="border-b pb-4 last:border-b-0">
                                                <div className="flex items-start gap-3">
                                                    <Avatar className="h-8 w-8 flex-shrink-0">
                                                        <AvatarImage src={comment.user?.profile_image} alt={comment.user?.name} />
                                                        <AvatarFallback className="text-xs">
                                                            {getInitials(comment.user?.name || 'U')}
                                                        </AvatarFallback>
                                                    </Avatar>
                                                    <div className="flex-1 min-w-0">
                                                        <div className="flex items-center gap-2 mb-1">
                                                            <span className="font-medium text-sm">
                                                                {comment.user?.name}
                                                            </span>
                                                            {comment.is_moderator_comment && (
                                                                <Badge variant="outline" className="text-xs">
                                                                    Moderator
                                                                </Badge>
                                                            )}
                                                            <span className="text-xs text-gray-500">
                                                                {formatDate(comment.created_at)}
                                                            </span>
                                                            {canDeleteComment(comment) && (
                                                                <Button
                                                                    variant="ghost"
                                                                    size="sm"
                                                                    onClick={() => handleDeleteComment(comment.id)}
                                                                    className="ml-auto h-6 w-6 p-0 text-red-500 hover:text-red-700 hover:bg-red-50"
                                                                >
                                                                    <Trash2Icon className="h-3 w-3" />
                                                                </Button>
                                                            )}
                                                        </div>
                                                        <p className="text-sm text-gray-700 whitespace-pre-wrap">
                                                            {comment.content}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        ))
                                    )}

                                    {/* Comment Form */}
                                    {!showCommentForm ? (
                                        <Button
                                            variant="outline"
                                            onClick={() => setShowCommentForm(true)}
                                            className="w-full"
                                        >
                                            Reactie toevoegen
                                        </Button>
                                    ) : (
                                        <form onSubmit={handleComment} className="space-y-3">
                                            <textarea
                                                value={commentData.content}
                                                onChange={(e) => setCommentData('content', e.target.value)}
                                                placeholder="Schrijf je reactie..."
                                                className="w-full p-3 border border-gray-300 rounded-lg resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                rows={3}
                                                maxLength={1000}
                                            />
                                            <div className="flex justify-between items-center">
                                                <span className="text-xs text-gray-500">
                                                    {commentData.content.length}/1000 karakters
                                                </span>
                                                <div className="flex gap-2">
                                                    <Button
                                                        type="button"
                                                        variant="outline"
                                                        size="sm"
                                                        onClick={() => {
                                                            setShowCommentForm(false);
                                                            resetComment();
                                                        }}
                                                    >
                                                        Annuleren
                                                    </Button>
                                                    <Button
                                                        type="submit"
                                                        size="sm"
                                                        disabled={commentProcessing || !commentData.content.trim()}
                                                    >
                                                        {commentProcessing ? 'Bezig...' : 'Reageren'}
                                                    </Button>
                                                </div>
                                            </div>
                                        </form>
                                    )}
                                </div>
                            </CardContent>
                        </Card>
                    </div>

                    {/* Sidebar */}
                    <div className="space-y-6">
                        {/* Status Timeline */}
                        <Card>
                            <CardHeader>
                                <CardTitle>Status Geschiedenis</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="space-y-3 text-sm">
                                    <div className="flex items-center gap-2">
                                        <div className="w-2 h-2 bg-gray-400 rounded-full"></div>
                                        <span className="text-gray-600">Ingediend</span>
                                        <span className="text-xs text-gray-500 ml-auto">
                                            {new Date(feedback.created_at).toLocaleDateString('nl-NL')}
                                        </span>
                                    </div>
                                    
                                    {feedback.status !== 'pending' && (
                                        <div className="flex items-center gap-2">
                                            <div className={`w-2 h-2 rounded-full ${
                                                feedback.status === 'approved' ? 'bg-green-500' : 
                                                feedback.status === 'rejected' ? 'bg-red-500' : 
                                                'bg-blue-500'
                                            }`}></div>
                                            <span>{statusLabels[feedback.status as keyof typeof statusLabels]}</span>
                                        </div>
                                    )}
                                </div>
                            </CardContent>
                        </Card>

                        {/* Statistics */}
                        <Card>
                            <CardHeader>
                                <CardTitle>Statistieken</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="space-y-3">
                                    <div className="flex justify-between">
                                        <span className="text-sm text-gray-600">Upvotes</span>
                                        <span className="text-sm font-medium text-green-600">{feedback.upvotes}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-sm text-gray-600">Downvotes</span>
                                        <span className="text-sm font-medium text-red-600">{feedback.downvotes}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-sm text-gray-600">Net Stemmen</span>
                                        <span className="text-sm font-medium">{feedback.upvotes - feedback.downvotes}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-sm text-gray-600">Reacties</span>
                                        <span className="text-sm font-medium">{feedback.comments.length}</span>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
