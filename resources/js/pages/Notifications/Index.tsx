import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { type SharedData, type Notification } from '@/types';
import { usePage } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Bell, BellOff, Trash2, ExternalLink } from 'lucide-react';

interface NotificationPageProps {
    notifications: {
        data: Notification[];
        links: any;
        meta: any;
    };
}

export default function NotificationsIndex() {
    const { notifications } = usePage<SharedData & NotificationPageProps>().props;

    const markAsRead = async (notificationId: number) => {
        try {
            await fetch(`/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
            });
            window.location.reload();
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    };

    const markAllAsRead = async () => {
        try {
            await fetch('/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
            });
            window.location.reload();
        } catch (error) {
            console.error('Error marking all notifications as read:', error);
        }
    };

    const deleteNotification = async (notificationId: number) => {
        if (!confirm('Weet je zeker dat je deze melding wilt verwijderen?')) {
            return;
        }

        try {
            await fetch(`/notifications/${notificationId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
            });
            window.location.reload();
        } catch (error) {
            console.error('Error deleting notification:', error);
        }
    };

    const getTypeIcon = (type: string) => {
        switch (type) {
            case 'activity':
                return '🎯';
            case 'match':
                return '🏓';
            case 'nieuws':
                return '📰';
            case 'profile_updated':
                return '👤';
            default:
                return '📢';
        }
    };

    const getTypeColor = (type: string) => {
        switch (type) {
            case 'activity':
                return 'text-blue-600';
            case 'match':
                return 'text-green-600';
            case 'nieuws':
                return 'text-purple-600';
            case 'profile_updated':
                return 'text-orange-600';
            default:
                return 'text-gray-600';
        }
    };

    const unreadNotifications = notifications.data.filter(n => !n.read_at);

    return (
        <>
            <Head title="Meldingen" />
            
            <div className="min-h-screen bg-gray-50 py-8">
                <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex justify-between items-center mb-8">
                        <div>
                            <h1 className="text-3xl font-bold text-gray-900">Meldingen</h1>
                            <p className="text-gray-600 mt-2">
                                {unreadNotifications.length > 0 
                                    ? `Je hebt ${unreadNotifications.length} ongelezen melding${unreadNotifications.length === 1 ? '' : 'en'}`
                                    : 'Alle meldingen zijn gelezen'
                                }
                            </p>
                        </div>
                        <div className="flex gap-2">
                            <Link href="/dashboard">
                                <Button variant="outline">
                                    Terug naar Dashboard
                                </Button>
                            </Link>
                            {unreadNotifications.length > 0 && (
                                <Button onClick={markAllAsRead} variant="outline">
                                    <BellOff className="w-4 h-4 mr-2" />
                                    Alles markeren als gelezen
                                </Button>
                            )}
                        </div>
                    </div>

                    <div className="bg-white shadow-sm rounded-lg">
                        {notifications.data.length === 0 ? (
                            <div className="p-8 text-center">
                                <Bell className="w-12 h-12 text-gray-400 mx-auto mb-4" />
                                <h3 className="text-lg font-medium text-gray-900 mb-2">Geen meldingen</h3>
                                <p className="text-gray-600">Je hebt nog geen meldingen ontvangen.</p>
                            </div>
                        ) : (
                            <div className="divide-y divide-gray-200">
                                {notifications.data.map((notification) => (
                                    <div
                                        key={notification.id}
                                        className={`p-4 hover:bg-gray-50 transition-colors ${
                                            !notification.read_at ? 'bg-blue-50 border-l-4 border-l-blue-500' : ''
                                        }`}
                                    >
                                        <div className="flex items-start justify-between">
                                            <div className="flex-1 min-w-0">
                                                <div className="flex items-center gap-3 mb-2">
                                                    <span className="text-2xl">{getTypeIcon(notification.type)}</span>
                                                    <div className="flex-1">
                                                        <h3 className="font-medium text-gray-900 truncate">
                                                            {notification.title}
                                                        </h3>
                                                        <p className={`text-xs font-medium uppercase tracking-wide ${getTypeColor(notification.type)}`}>
                                                            {notification.type === 'activity' && 'Activiteit'}
                                                            {notification.type === 'match' && 'Wedstrijd'}
                                                            {notification.type === 'nieuws' && 'Nieuws'}
                                                            {notification.type === 'profile_updated' && 'Profiel'}
                                                            {notification.type === 'general' && 'Algemeen'}
                                                        </p>
                                                    </div>
                                                    {!notification.read_at && (
                                                        <div className="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0"></div>
                                                    )}
                                                </div>
                                                
                                                <p className="text-gray-700 mb-3">
                                                    {notification.message}
                                                </p>
                                                
                                                <div className="flex items-center justify-between">
                                                    <p className="text-xs text-gray-500">
                                                        {new Date(notification.created_at).toLocaleDateString('nl-NL', {
                                                            day: 'numeric',
                                                            month: 'long',
                                                            year: 'numeric',
                                                            hour: '2-digit',
                                                            minute: '2-digit'
                                                        })}
                                                    </p>
                                                    
                                                    <div className="flex items-center gap-2">
                                                        {notification.data?.url && (
                                                            <Link 
                                                                href={notification.data.url}
                                                                className="text-blue-600 hover:text-blue-800 text-sm inline-flex items-center gap-1"
                                                            >
                                                                Bekijken <ExternalLink className="w-3 h-3" />
                                                            </Link>
                                                        )}
                                                        
                                                        {!notification.read_at && (
                                                            <Button
                                                                variant="ghost"
                                                                size="sm"
                                                                onClick={() => markAsRead(notification.id)}
                                                                className="text-xs"
                                                            >
                                                                Markeren als gelezen
                                                            </Button>
                                                        )}
                                                        
                                                        <Button
                                                            variant="ghost"
                                                            size="sm"
                                                            onClick={() => deleteNotification(notification.id)}
                                                            className="text-red-600 hover:text-red-800"
                                                        >
                                                            <Trash2 className="w-4 h-4" />
                                                        </Button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>

                    {/* Pagination */}
                    {notifications.links && (
                        <div className="mt-6 flex justify-center">
                            <div className="flex gap-2">
                                {notifications.links.map((link: any, index: number) => (
                                    <Link
                                        key={index}
                                        href={link.url || '#'}
                                        className={`px-3 py-2 text-sm rounded-md ${
                                            link.active
                                                ? 'bg-blue-600 text-white'
                                                : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300'
                                        } ${!link.url ? 'opacity-50 cursor-not-allowed' : ''}`}
                                        preserveScroll
                                        preserveState
                                    >
                                        <span dangerouslySetInnerHTML={{ __html: link.label }} />
                                    </Link>
                                ))}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </>
    );
}
