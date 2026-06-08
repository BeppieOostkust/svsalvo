import '../css/app.css';

import { createInertiaApp } from '@inertiajs/react';
import { usePage } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { ReactNode } from 'react';
import { useEffect } from 'react';
import { createRoot } from 'react-dom/client';
import { initializeTheme } from './hooks/use-appearance';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./pages/${name}.tsx`, import.meta.glob('./pages/**/*.tsx')),
    setup({ el, App, props }) {
        const root = createRoot(el);

        root.render(
            <App {...props} />
        );
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on load...
initializeTheme();

const s = 'background: #2563EB; color: #fff; font-size: 13px; padding: 8px 20px;';
const sFirst = 'background: #2563EB; color: #fff; font-size: 15px; font-weight: bold; padding: 12px 20px 8px;';
const sLast = 'background: #2563EB; color: #fff; font-size: 13px; padding: 8px 20px 12px;';

console.log('%c👋 Hé developer!', sFirst);
console.log('%cGoed bezig dat je hier kijkt. Wij bouwen dit soort dingen bij Briqq.', s);
console.log('%cInteresse? → https://briqq.nl/contact', sLast);