import { type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import Header from '@/components/header';
import { GeistSans } from 'geist/font/sans';
import { GeistMono } from 'geist/font/mono';



export default function Welcome() {
    const { auth } = usePage<SharedData>().props;

    return (
        <>
            <Head title="Welcome">
                <link rel="preconnect" href="https://fonts.googleapis.com" />
                <link rel="preconnect" href="https://fonts.gstatic.com" />
                <link href="https://fonts.googleapis.com/css2?family=Geist:wght@100..900&display=swap" rel="stylesheet" />

            </Head>
            
            <Header />
        </>
    );
}
