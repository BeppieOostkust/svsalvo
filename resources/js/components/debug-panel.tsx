import React, { useState, useEffect } from 'react';
import { Link, usePage } from '@inertiajs/react';
import { type SharedData } from '@/types';

interface User {
    id: number;
    name: string;
    email: string;
    is_admin: boolean;
}

export default function DebugPanel() {
    const { auth } = usePage<SharedData>().props;
    const [isOpen, setIsOpen] = useState(false);
    const [users, setUsers] = useState<User[]>([]);
    const [showUsers, setShowUsers] = useState(false);

    // Show debug panel if in development OR if explicitly enabled via localStorage
    // For this project, always show debug panel
    const isDebugEnabled = true; // Always enabled for development purposes
    
    if (!isDebugEnabled) {
        return null;
    }

    const fetchUsers = async () => {
        if (!showUsers) {
            try {
                const response = await fetch('/debug/users');
                const userData = await response.json();
                setUsers(userData);
                setShowUsers(true);
            } catch (error) {
                console.error('Failed to fetch users:', error);
            }
        } else {
            setShowUsers(false);
        }
    };

    // Add keyboard shortcut to toggle debug panel in production
    React.useEffect(() => {
        const handleKeyPress = (event: KeyboardEvent) => {
            // Ctrl + Shift + D to toggle debug panel
            if (event.ctrlKey && event.shiftKey && event.key === 'D') {
                const isEnabled = localStorage.getItem('debug_panel_enabled') === 'true';
                localStorage.setItem('debug_panel_enabled', (!isEnabled).toString());
                window.location.reload(); // Reload to apply changes
            }
        };

        window.addEventListener('keydown', handleKeyPress);
        return () => window.removeEventListener('keydown', handleKeyPress);
    }, []);

    return (
        <div className="fixed bottom-4 right-4 z-50">
            {/* Debug Toggle Button */}
            <button
                onClick={() => setIsOpen(!isOpen)}
                className="bg-red-600 hover:bg-red-700 text-white p-2 rounded-full shadow-lg transition-colors"
                title="Debug Panel"
            >
                🐛
            </button>

            {/* Debug Panel */}
            {isOpen && (
                <div className="absolute bottom-12 right-0 bg-white border border-gray-300 rounded-lg shadow-xl p-4 min-w-[300px]">
                    <div className="flex justify-between items-center mb-3">
                        <h3 className="font-bold text-gray-800">Debug Panel</h3>
                        <button
                            onClick={() => setIsOpen(false)}
                            className="text-gray-500 hover:text-gray-700"
                        >
                            ✕
                        </button>
                    </div>

                    {/* Current User Info */}
                    <div className="mb-4 p-2 bg-gray-50 rounded">
                        {auth.user ? (
                            <div>
                                <p className="text-sm font-medium">Logged in as:</p>
                                <p className="text-sm text-gray-600">{auth.user.name}</p>
                                <p className="text-xs text-gray-500">{auth.user.email}</p>
                                {(auth.user as any).is_admin && (
                                    <span className="inline-block px-2 py-1 text-xs bg-red-100 text-red-800 rounded mt-1">
                                        Admin
                                    </span>
                                )}
                            </div>
                        ) : (
                            <p className="text-sm text-gray-600">Not logged in</p>
                        )}
                    </div>

                    {/* Quick Actions */}
                    <div className="space-y-2">
                        {auth.user ? (
                            <Link
                                href="/debug/logout"
                                className="block w-full text-center bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded text-sm transition-colors"
                            >
                                🚪 Logout
                            </Link>
                        ) : (
                            <Link
                                href="/debug/login"
                                className="block w-full text-center bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded text-sm transition-colors"
                            >
                                🔐 Quick Login (First User)
                            </Link>
                        )}

                        <button
                            onClick={fetchUsers}
                            className="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-sm transition-colors"
                        >
                            👥 {showUsers ? 'Hide' : 'Show'} All Users
                        </button>

                        {/* User List */}
                        {showUsers && (
                            <div className="mt-3 max-h-48 overflow-y-auto border border-gray-200 rounded">
                                {users.map((user) => (
                                    <div key={user.id} className="p-2 border-b border-gray-100 last:border-b-0">
                                        <div className="flex justify-between items-start">
                                            <div className="flex-1 min-w-0">
                                                <p className="text-sm font-medium text-gray-900 truncate">
                                                    {user.name}
                                                </p>
                                                <p className="text-xs text-gray-500 truncate">
                                                    {user.email}
                                                </p>
                                                {user.is_admin && (
                                                    <span className="inline-block px-1 py-0.5 text-xs bg-red-100 text-red-800 rounded mt-1">
                                                        Admin
                                                    </span>
                                                )}
                                            </div>
                                            <Link
                                                href={`/debug/login/${user.id}`}
                                                className="ml-2 text-xs bg-blue-100 hover:bg-blue-200 text-blue-800 px-2 py-1 rounded transition-colors"
                                            >
                                                Login
                                            </Link>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}

                        {/* Quick Links */}
                        <div className="pt-2 border-t border-gray-200">
                            <p className="text-xs text-gray-500 mb-2">Quick Links:</p>
                            <div className="space-y-1">
                                <Link
                                    href="/admin"
                                    className="block text-xs text-blue-600 hover:text-blue-800"
                                >
                                    🛠️ Admin Panel
                                </Link>
                                <Link
                                    href="/dashboard"
                                    className="block text-xs text-blue-600 hover:text-blue-800"
                                >
                                    📊 Dashboard
                                </Link>
                                <Link
                                    href="/profile"
                                    className="block text-xs text-blue-600 hover:text-blue-800"
                                >
                                    👤 Profile
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
