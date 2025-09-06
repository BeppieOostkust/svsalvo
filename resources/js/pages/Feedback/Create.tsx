import React, { useState } from 'react';
import { Head, useForm } from '@inertiajs/react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Checkbox } from '@/components/ui/checkbox';
import { Alert, AlertDescription } from '@/components/ui/alert';
import Layout from '@/components/Layout';
import { ArrowLeftIcon, LightbulbIcon, MessageSquareIcon, WrenchIcon, BugIcon, SparklesIcon } from 'lucide-react';

const typeOptions = [
    {
        value: 'idea',
        label: 'Idee',
        description: 'Een nieuw idee voor de vereniging',
        icon: LightbulbIcon,
        color: 'text-blue-600'
    },
    {
        value: 'feedback',
        label: 'Feedback',
        description: 'Feedback op bestaande processen of activiteiten',
        icon: MessageSquareIcon,
        color: 'text-green-600'
    },
    {
        value: 'suggestion',
        label: 'Suggestie',
        description: 'Een suggestie voor verbetering',
        icon: SparklesIcon,
        color: 'text-purple-600'
    },
    {
        value: 'bug_report',
        label: 'Bug Report',
        description: 'Meld een probleem met de website',
        icon: BugIcon,
        color: 'text-red-600'
    },
    {
        value: 'feature_request',
        label: 'Feature Verzoek',
        description: 'Vraag om een nieuwe functionaliteit',
        icon: WrenchIcon,
        color: 'text-indigo-600'
    },
];

export default function FeedbackCreate() {
    const { data, setData, post, processing, errors } = useForm({
        title: '',
        description: '',
        type: '',
        is_anonymous: false as boolean,
    });

    const [selectedType, setSelectedType] = useState<typeof typeOptions[0] | null>(null);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('feedback.store'));
    };

    const handleTypeChange = (value: string) => {
        setData('type', value);
        const type = typeOptions.find(t => t.value === value);
        setSelectedType(type || null);
    };

    return (
        <Layout>
            <Head title="Nieuwe Feedback" />

            <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                {/* Header */}
                <div className="mb-8">
                    <div className="flex items-center gap-4 mb-4">
                        <Button
                            variant="outline"
                            onClick={() => window.history.back()}
                            className="flex items-center gap-2"
                        >
                            <ArrowLeftIcon className="h-4 w-4" />
                            Terug
                        </Button>
                    </div>
                    <h1 className="text-3xl sm:text-4xl font-bold text-gray-900 flex items-center gap-3">
                        ✍️ Nieuwe Feedback
                    </h1>
                    <p className="mt-2 text-gray-600">
                        Deel je ideeën, feedback of suggesties om onze vereniging te verbeteren.
                    </p>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {/* Form */}
                    <div className="lg:col-span-2">
                        <Card>
                            <CardHeader>
                                <CardTitle>Feedback Details</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <form onSubmit={handleSubmit} className="space-y-6">
                                    {/* Type Selection */}
                                    <div>
                                        <Label htmlFor="type">Type *</Label>
                                        <Select value={data.type} onValueChange={handleTypeChange}>
                                            <SelectTrigger className="w-full mt-2">
                                                <SelectValue placeholder="Selecteer het type feedback" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                {typeOptions.map((type) => {
                                                    const Icon = type.icon;
                                                    return (
                                                        <SelectItem key={type.value} value={type.value}>
                                                            <div className="flex items-center gap-2">
                                                                <Icon className={`h-4 w-4 ${type.color}`} />
                                                                <div>
                                                                    <div className="font-medium">{type.label}</div>
                                                                    <div className="text-xs text-gray-500">{type.description}</div>
                                                                </div>
                                                            </div>
                                                        </SelectItem>
                                                    );
                                                })}
                                            </SelectContent>
                                        </Select>
                                        {errors.type && (
                                            <p className="text-sm text-red-600 mt-1">{errors.type}</p>
                                        )}
                                    </div>

                                    {/* Title */}
                                    <div>
                                        <Label htmlFor="title">Titel *</Label>
                                        <Input
                                            id="title"
                                            type="text"
                                            value={data.title}
                                            onChange={(e) => setData('title', e.target.value)}
                                            placeholder="Korte, beschrijvende titel voor je feedback"
                                            className="mt-2"
                                            maxLength={255}
                                        />
                                        {errors.title && (
                                            <p className="text-sm text-red-600 mt-1">{errors.title}</p>
                                        )}
                                        <p className="text-xs text-gray-500 mt-1">
                                            {data.title.length}/255 karakters
                                        </p>
                                    </div>

                                    {/* Description */}
                                    <div>
                                        <Label htmlFor="description">Beschrijving *</Label>
                                        <textarea
                                            id="description"
                                            value={data.description}
                                            onChange={(e: React.ChangeEvent<HTMLTextAreaElement>) => setData('description', e.target.value)}
                                            placeholder="Beschrijf je feedback, idee of suggestie in detail..."
                                            className="mt-2 min-h-[150px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                            maxLength={2000}
                                        />
                                        {errors.description && (
                                            <p className="text-sm text-red-600 mt-1">{errors.description}</p>
                                        )}
                                        <p className="text-xs text-gray-500 mt-1">
                                            {data.description.length}/2000 karakters
                                        </p>
                                    </div>

                                    {/* Anonymous Option */}
                                    <div className="flex items-center space-x-2">
                                        <Checkbox
                                            id="is_anonymous"
                                            checked={data.is_anonymous}
                                            onCheckedChange={(checked) => setData('is_anonymous', !!checked)}
                                        />
                                        <Label htmlFor="is_anonymous" className="text-sm">
                                            Anoniem indienen (je naam wordt niet getoond)
                                        </Label>
                                    </div>

                                    {/* Submit Button */}
                                    <div className="flex gap-4">
                                        <Button
                                            type="submit"
                                            disabled={processing || !data.title || !data.description || !data.type}
                                            className="bg-blue-600 hover:bg-blue-700"
                                        >
                                            {processing ? 'Bezig met versturen...' : 'Feedback Versturen'}
                                        </Button>
                                        <Button
                                            type="button"
                                            variant="outline"
                                            onClick={() => window.history.back()}
                                        >
                                            Annuleren
                                        </Button>
                                    </div>
                                </form>
                            </CardContent>
                        </Card>
                    </div>

                    {/* Sidebar */}
                    <div className="space-y-6">
                        {/* Selected Type Info */}
                        {selectedType && (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <selectedType.icon className={`h-5 w-5 ${selectedType.color}`} />
                                        {selectedType.label}
                                    </CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <p className="text-sm text-gray-600">
                                        {selectedType.description}
                                    </p>
                                </CardContent>
                            </Card>
                        )}

                        {/* Guidelines */}
                        <Card>
                            <CardHeader>
                                <CardTitle>Richtlijnen</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-3">
                                <div className="text-sm text-gray-600">
                                    <h4 className="font-medium text-gray-900 mb-2">Voor goede feedback:</h4>
                                    <ul className="space-y-1">
                                        <li>• Wees specifiek en duidelijk</li>
                                        <li>• Geef context waar mogelijk</li>
                                        <li>• Blijf respectvol en constructief</li>
                                        <li>• Vermeld concrete voorstellen</li>
                                    </ul>
                                </div>
                            </CardContent>
                        </Card>

                        {/* Process Info */}
                        <Card>
                            <CardHeader>
                                <CardTitle>Wat gebeurt er hierna?</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="space-y-3 text-sm text-gray-600">
                                    <div className="flex items-start gap-2">
                                        <div className="w-2 h-2 bg-yellow-500 rounded-full mt-2 flex-shrink-0"></div>
                                        <div>
                                            <div className="font-medium">1. In afwachting</div>
                                            <div>Je feedback wordt beoordeeld door moderators</div>
                                        </div>
                                    </div>
                                    <div className="flex items-start gap-2">
                                        <div className="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                                        <div>
                                            <div className="font-medium">2. In behandeling</div>
                                            <div>Feedback wordt besproken en onderzocht</div>
                                        </div>
                                    </div>
                                    <div className="flex items-start gap-2">
                                        <div className="w-2 h-2 bg-green-500 rounded-full mt-2 flex-shrink-0"></div>
                                        <div>
                                            <div className="font-medium">3. Goedgekeurd</div>
                                            <div>Feedback is goedgekeurd en wordt mogelijk geïmplementeerd</div>
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        {/* Privacy Notice */}
                        <Alert>
                            <AlertDescription className="text-xs">
                                Je feedback wordt beoordeeld door moderators en kan zichtbaar worden voor alle leden. 
                                Persoonlijke informatie wordt niet gedeeld tenzij je hiervoor toestemming geeft.
                            </AlertDescription>
                        </Alert>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
