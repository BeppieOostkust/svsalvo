import React from 'react';
import { usePage } from '@inertiajs/react';
import UrgentArticles from '@/components/urgent-articles';
import { type SharedData } from '@/types';

export default function GlobalUrgentBanner() {
    const { urgentArticles } = usePage<SharedData>().props;

    return <UrgentArticles urgentArticles={urgentArticles} />;
}
