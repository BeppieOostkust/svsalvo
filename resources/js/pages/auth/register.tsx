import { Head, useForm } from '@inertiajs/react';
import { LoaderCircle, Calendar, Phone, Mail, User } from 'lucide-react';
import { FormEventHandler, useEffect } from 'react';

import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/auth-layout';

type MembershipApplicationForm = {
    voornaam: string;
    achternaam: string;
    email: string;
    telefoonnummer: string;
    geboortedatum: string;
};

export default function Register() {
    const { data, setData, post, processing, errors, reset } = useForm<Required<MembershipApplicationForm>>({
        voornaam: '',
        achternaam: '',
        email: '',
        telefoonnummer: '',
        geboortedatum: '',
    });

    // Calculate age from birth date
    const calculateAge = (birthDate: string): number => {
        if (!birthDate) return 0;
        const today = new Date();
        const birth = new Date(birthDate);
        let age = today.getFullYear() - birth.getFullYear();
        const monthDiff = today.getMonth() - birth.getMonth();
        
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
            age--;
        }
        
        return age;
    };

    const currentAge = calculateAge(data.geboortedatum);

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('membership.apply'), {
            onSuccess: () => {
                window.location.href = route('membership.success');
            }
        });
    };

    return (
        <AuthLayout 
            title="Lidmaatschap Aanvragen" 
            description="Vraag je lidmaatschap aan voor SSV De Moes - Schietsportvereniging"
        >
            <Head title="Lidmaatschap Aanvragen">
                <link rel="preconnect" href="https://fonts.googleapis.com" />
                <link rel="preconnect" href="https://fonts.gstatic.com" />
                <link href="https://fonts.googleapis.com/css2?family=Geist:wght@100..900&display=swap" rel="stylesheet" />
            </Head>

            <form className="flex flex-col gap-6" onSubmit={submit}>
                <div className="grid gap-6">
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div className="grid gap-2">
                            <Label htmlFor="voornaam" className="flex items-center gap-2">
                                <User className="h-4 w-4" />
                                Voornaam
                            </Label>
                            <Input
                                id="voornaam"
                                value={data.voornaam}
                                onChange={(e) => setData('voornaam', e.target.value)}
                                placeholder="Je voornaam"
                                required
                                autoComplete="given-name"
                            />
                            <InputError message={errors.voornaam} />
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="achternaam" className="flex items-center gap-2">
                                <User className="h-4 w-4" />
                                Achternaam
                            </Label>
                            <Input
                                id="achternaam"
                                value={data.achternaam}
                                onChange={(e) => setData('achternaam', e.target.value)}
                                placeholder="Je achternaam"
                                required
                                autoComplete="family-name"
                            />
                            <InputError message={errors.achternaam} />
                        </div>
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="email" className="flex items-center gap-2">
                            <Mail className="h-4 w-4" />
                            E-mailadres
                        </Label>
                        <Input
                            id="email"
                            type="email"
                            value={data.email}
                            onChange={(e) => setData('email', e.target.value)}
                            placeholder="naam@voorbeeld.nl"
                            required
                            autoComplete="email"
                        />
                        <InputError message={errors.email} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="telefoonnummer" className="flex items-center gap-2">
                            <Phone className="h-4 w-4" />
                            Telefoonnummer
                        </Label>
                        <Input
                            id="telefoonnummer"
                            type="tel"
                            value={data.telefoonnummer}
                            onChange={(e) => setData('telefoonnummer', e.target.value)}
                            placeholder="06-12345678"
                            required
                            autoComplete="tel"
                        />
                        <InputError message={errors.telefoonnummer} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="geboortedatum" className="flex items-center gap-2">
                            <Calendar className="h-4 w-4" />
                            Geboortedatum
                        </Label>
                        <Input
                            id="geboortedatum"
                            type="date"
                            value={data.geboortedatum}
                            onChange={(e) => setData('geboortedatum', e.target.value)}
                            required
                            max={new Date().toISOString().split('T')[0]}
                            autoComplete="bday"
                        />
                        {currentAge > 0 && (
                            <p className="text-sm text-muted-foreground">
                                Leeftijd: {currentAge} jaar
                            </p>
                        )}
                        <InputError message={errors.geboortedatum} />
                    </div>
                </div>

                <Button disabled={processing} type="submit" className="w-full">
                    {processing && <LoaderCircle className="h-4 w-4 animate-spin" />}
                    Lidmaatschap Aanvragen
                </Button>

                <div className="text-center text-sm">
                    <span className="text-muted-foreground">Al lid? </span>
                    <TextLink href={route('login')}>Log hier in</TextLink>
                </div>

                <div className="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <h3 className="font-semibold text-blue-900 mb-2">Welkom bij SSV De Moes!</h3>
                    <p className="text-sm text-blue-800 mb-2">
                        Vul dit formulier in om je lidmaatschap aan te vragen voor onze schietsportvereniging.
                    </p>
                    <ul className="text-sm text-blue-800 list-disc list-inside space-y-1">
                        <li>Je aanvraag wordt beoordeeld door het bestuur</li>
                        <li>We nemen binnen 5 werkdagen contact met je op</li>
                        <li>Voor vragen kun je ons bellen: +31 33 123 4567</li>
                    </ul>
                </div>
            </form>
        </AuthLayout>
    );
}
