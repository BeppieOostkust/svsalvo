import React from 'react';
import Header from './header';
import Footer from './footer';

interface LayoutProps {
    children: React.ReactNode;
    showHeader?: boolean;
    showFooter?: boolean;
}

export default function Layout({ children, showHeader = true, showFooter = true }: LayoutProps) {
    return (
        <div className="min-h-screen h-full flex flex-col">
            {showHeader && <Header />}
            
            <main className="flex-1">
                {children}
            </main>
            
            {showFooter && (
                <Footer />
            )}
        </div>
    );
}
