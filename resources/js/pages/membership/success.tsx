import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { CheckCircle, Home, Phone, Mail } from 'lucide-react';
import { Button } from '@/components/ui/button';
import AuthLayout from '@/layouts/auth-layout';

export default function MembershipSuccess() {
    return (
        <AuthLayout 
            title="Aanvraag Verzonden!" 
            description="Je lidmaatschap aanvraag is succesvol verzonden"
        >
            <Head title="Aanvraag Verzonden">
                <link rel="preconnect" href="https://fonts.googleapis.com" />
                <link rel="preconnect" href="https://fonts.gstatic.com" />
                <link href="https://fonts.googleapis.com/css2?family=Geist:wght@100..900&display=swap" rel="stylesheet" />
            </Head>

            <div className="text-center space-y-6">
                <div className="flex justify-center">
                    <CheckCircle className="h-16 w-16 text-green-500" />
                </div>
                
                <div className="space-y-2">
                    <h2 className="text-2xl font-bold text-gray-900">
                        Bedankt voor je aanvraag!
                    </h2>
                    <p className="text-gray-600">
                        Je lidmaatschap aanvraag is succesvol verzonden.
                    </p>
                </div>

                <div className="bg-green-50 border border-green-200 rounded-lg p-6 text-left">
                    <h3 className="font-semibold text-green-900 mb-3">Wat gebeurt er nu?</h3>
                    <ul className="space-y-2 text-sm text-green-800">
                        <li className="flex items-start gap-2">
                            <span className="font-medium">1.</span>
                            <span>Je aanvraag wordt beoordeeld door ons bestuur</span>
                        </li>
                        <li className="flex items-start gap-2">
                            <span className="font-medium">2.</span>
                            <span>We nemen binnen 5 werkdagen contact met je op</span>
                        </li>
                        <li className="flex items-start gap-2">
                            <span className="font-medium">3.</span>
                            <span>Bij goedkeuring ontvang je informatie over de vervolgstappen</span>
                        </li>
                    </ul>
                </div>

                <div className="bg-blue-50 border border-blue-200 rounded-lg p-6 text-left">
                    <h3 className="font-semibold text-blue-900 mb-3">Contact</h3>
                    <div className="space-y-2 text-sm text-blue-800">
                        <div className="flex items-center gap-2">
                            <Mail className="h-4 w-4" />
                            <span>info@ssvdemoes.nl</span>
                        </div>
                        <p className="mt-3">
                            Heb je vragen over je aanvraag? Neem gerust contact met ons op!
                        </p>
                    </div>
                </div>

                <div className="flex flex-col sm:flex-row gap-3 justify-center">
                    <Link href={route('home')}>
                        <Button variant="outline" className="w-full sm:w-auto">
                            <Home className="h-4 w-4 mr-2" />
                            Terug naar website
                        </Button>
                    </Link>
                    <Link href={route('login')}>
                        <Button className="w-full sm:w-auto">
                            Al lid? Log in
                        </Button>
                    </Link>
                </div>
            </div>
        </AuthLayout>
    );
}
