import React from 'react';
import { type SharedData, type Notification } from '@/types';
import {
    NavigationMenu,
    NavigationMenuContent,
    NavigationMenuItem,
    NavigationMenuLink,
    NavigationMenuList,
    NavigationMenuTrigger,
  } from "@/components/ui/navigation-menu"
  import { navigationMenuTriggerStyle } from "@/components/ui/navigation-menu"
  import { cn } from "@/lib/utils"
  import { Button } from "@/components/ui/button"
  import { Link, usePage, Head } from '@inertiajs/react';
  import DebugPanel from '@/components/debug-panel';
  import UrgentArticles from '@/components/urgent-articles';
  import { Menu, X, Bell, BellDot } from 'lucide-react';

export default function Header() {
    const { auth, urgentArticles, notifications } = usePage<SharedData>().props;
    const [bannerDismissed, setBannerDismissed] = React.useState(false);
    const [mobileMenuOpen, setMobileMenuOpen] = React.useState(false);
    const [notificationOpen, setNotificationOpen] = React.useState(false);
    
    // Check if we have urgent articles to display
    const hasUrgentArticles = urgentArticles && Array.isArray(urgentArticles) && urgentArticles.length > 0;
    
    // Check if any urgent articles are dismissed
    const [dismissedArticleIds, setDismissedArticleIds] = React.useState<number[]>([]);
    const visibleUrgentArticles = hasUrgentArticles 
        ? urgentArticles.filter(article => !dismissedArticleIds.includes(article.id))
        : [];
    const hasVisibleUrgentArticles = visibleUrgentArticles.length > 0;
    
    const shouldShowBanner = hasVisibleUrgentArticles && !bannerDismissed;

    // Notification system
    const userNotifications: Notification[] = notifications || [];
    const unreadNotifications = userNotifications.filter((notification: Notification) => !notification.read_at);
    const hasUnreadNotifications = unreadNotifications.length > 0;

    // Mark notification as read
    const markAsRead = async (notificationId: number) => {
        try {
            await fetch(`/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
            });
            // Refresh page to update notifications
            window.location.reload();
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    };

    // Mark all notifications as read
    const markAllAsRead = async () => {
        try {
            await fetch('/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
            });
            // Refresh page to update notifications
            window.location.reload();
        } catch (error) {
            console.error('Error marking all notifications as read:', error);
        }
    };

    // Debug logging
    console.log('Header render:', { 
        hasUrgentArticles, 
        hasVisibleUrgentArticles, 
        bannerDismissed, 
        shouldShowBanner,
        dismissedArticleIds 
    });

    // Load dismissed articles from localStorage on mount
    React.useEffect(() => {
        const dismissed = localStorage.getItem('dismissedUrgentArticles');
        if (dismissed) {
            const parsedDismissed = JSON.parse(dismissed);
            setDismissedArticleIds(parsedDismissed);
            console.log('Header loaded dismissed articles:', parsedDismissed);
        }
    }, []);

    // Listen for banner dismissal events
    React.useEffect(() => {
        const handleBannerDismissed = () => {
            console.log('Banner dismissed event received in Header');
            setBannerDismissed(true);
        };

        window.addEventListener('urgentBannerDismissed', handleBannerDismissed);
        
        return () => {
            window.removeEventListener('urgentBannerDismissed', handleBannerDismissed);
        };
    }, []);

    return (
        <div>
            
            {/* Urgent Articles Banner */}
            {shouldShowBanner && (
                <UrgentArticles urgentArticles={urgentArticles} />
            )}
            
            {/* Main Header - add top padding when urgent banner is present and not dismissed */}
            <div className={`my-2 lg:my-4 mx-auto p-2 lg:p-4 w-[95%] lg:w-[90%] border-gray-400 shadow-xl rounded-lg flex flex-row justify-between items-center transition-all duration-500 ease-in-out ${shouldShowBanner ? 'mt-20' : 'mt-2 lg:mt-4'}`}>
                <Link href={route("dashboard.home")} className='flex flex-row items-center gap-2 lg:gap-4 hover:opacity-80 transition-opacity cursor-pointer min-w-0'>
                    <img src="/images/logo.png" alt="SSV De Moes" className='rounded w-8 lg:w-12 flex-shrink-0'/>
                    <span className='text-lg lg:text-2xl font-bold hidden lg:inline truncate'>SSV De Moes</span>
                </Link>
                
                {/* Mobile hamburger menu button */}
                <div className="lg:hidden">
                    <Button 
                        variant="ghost" 
                        size="sm" 
                        onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
                        className="p-1 h-8 w-8"
                    >
                        {mobileMenuOpen ? <X className="h-5 w-5" /> : <Menu className="h-5 w-5" />}
                    </Button>
                </div>

                {/* Desktop navigation */}
                <div className="hidden lg:block">
                    <nav className='flex flex-row gap-4 font-semibold'>
                        <NavigationMenu>
                            <NavigationMenuList>
                                
                                <NavigationMenuItem>
                                    <Link href={route("nieuws")}>
                                        <NavigationMenuLink className={navigationMenuTriggerStyle()}>
                                        Nieuws
                                        </NavigationMenuLink>
                                    </Link>
                                </NavigationMenuItem>
                                <NavigationMenuItem>
                                    <Link href={route("downloads")}>
                                        <NavigationMenuLink className={navigationMenuTriggerStyle()}>
                                        Downloads
                                        </NavigationMenuLink>
                                    </Link>
                                </NavigationMenuItem>
                                <NavigationMenuItem>
                                    <Link href="/activiteiten" className={navigationMenuTriggerStyle()}>
                                        Activiteiten
                                    </Link>
                                </NavigationMenuItem>
                                <NavigationMenuItem>
                                    <Link href={route("vereniging")}>
                                        <NavigationMenuLink className={navigationMenuTriggerStyle()}>
                                        Leden
                                        </NavigationMenuLink>
                                    </Link>
                                </NavigationMenuItem>
                                <NavigationMenuItem>
                                    <Link href={route("organisatie")}>
                                        <NavigationMenuLink className={navigationMenuTriggerStyle()}>
                                        Over Ons
                                        </NavigationMenuLink>
                                    </Link>
                                </NavigationMenuItem>
                                <NavigationMenuItem>
                                    <Link href={route("regels")}>
                                        <NavigationMenuLink className={navigationMenuTriggerStyle()}>
                                        Regels
                                        </NavigationMenuLink>
                                    </Link>
                                </NavigationMenuItem>
                                <NavigationMenuItem>
                                    <Link href={route("wedstrijden.index")}>
                                        <NavigationMenuLink className={navigationMenuTriggerStyle()}>
                                        Wedstrijden
                                        </NavigationMenuLink>
                                    </Link>
                                </NavigationMenuItem>
                            </NavigationMenuList>
                        </NavigationMenu>
                    </nav>
                </div>

                {/* Desktop auth buttons */}
                <div className="hidden lg:flex gap-1 lg:gap-2 flex-shrink-0">
                    {/* Notification Bell - only show for authenticated users */}
                    {auth.user && (
                        <div className="relative">
                            <Button
                                variant="ghost"
                                size="sm"
                                onClick={() => setNotificationOpen(!notificationOpen)}
                                className="p-1 h-8 w-8"
                            >
                                {hasUnreadNotifications ? (
                                    <BellDot className="h-5 w-5 text-red-500" />
                                ) : (
                                    <Bell className="h-5 w-5" />
                                )}
                            </Button>
                            {hasUnreadNotifications && (
                                <span className="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                    {unreadNotifications.length > 9 ? '9+' : unreadNotifications.length}
                                </span>
                            )}
                        </div>
                    )}
                {auth.user ? (
                    <>
                        <Link
                            href={route('dashboard')}
                        >
                            <Button className="text-xs lg:text-sm px-1 lg:px-4 py-1 lg:py-2 h-8 lg:h-auto">
                                <span className="hidden sm:inline">Dashboard</span>
                                <span className="sm:hidden">Dashboard</span>
                            </Button>
                        </Link>
                        {(auth.user.is_admin || (auth.user.roles && auth.user.roles.length > 0)) && (
                            <Link
                                href="/admin"
                            >
                                <Button variant="outline" className="border-red-500 text-red-600 hover:bg-red-50 hover:text-red-700 text-xs lg:text-sm px-1 lg:px-4 py-1 lg:py-2 h-8 lg:h-auto">
                                    Admin
                                </Button>
                            </Link>
                        )}
                        <Link
                            href={route('logout')}
                            method="post"
                            as="button"
                        >
                            <Button variant="outline" className="border-gray-300 text-gray-600 hover:bg-gray-50 hover:text-gray-700 text-xs lg:text-sm px-1 lg:px-4 py-1 lg:py-2 h-8 lg:h-auto">
                                <span className="hidden sm:inline">Uitloggen</span>
                                <span className="sm:hidden">Uitloggen</span>
                            </Button>
                        </Link>
                    </>
                ) : (
                    <>
                        <Link
                            href={route('login')}
                        >
                            <Button variant={'secondary'} className="text-xs lg:text-sm px-1 lg:px-4 py-1 lg:py-2 h-8 lg:h-auto">
                                <span className="hidden sm:inline">Log in</span>
                                <span className="sm:hidden">In</span>
                            </Button>
                        </Link>
                        <Link
                            href={route('register')}
                        >
                            <Button className="text-xs lg:text-sm px-1 lg:px-4 py-1 lg:py-2 h-8 lg:h-auto">
                                <span className="hidden sm:inline">Lid worden</span>
                                <span className="sm:hidden">Lid</span>
                            </Button>
                        </Link>
                    </>
                )}
                </div>
            </div>

            {/* Mobile menu overlay */}
            {mobileMenuOpen && (
                <div className="lg:hidden fixed inset-0 z-50 bg-black bg-opacity-50" onClick={() => setMobileMenuOpen(false)}>
                    <div className="fixed top-0 right-0 h-full w-80 max-w-[85vw] bg-white shadow-xl" onClick={(e) => e.stopPropagation()}>
                        <div className="p-4">
                            <div className="flex justify-between items-center mb-6">
                                <h2 className="text-lg font-semibold">Menu</h2>
                                <Button 
                                    variant="ghost" 
                                    size="sm" 
                                    onClick={() => setMobileMenuOpen(false)}
                                    className="p-1 h-8 w-8"
                                >
                                    <X className="h-5 w-5" />
                                </Button>
                            </div>
                            
                            {/* Mobile Navigation Links */}
                            <nav className="space-y-2 mb-6">
                                <Link 
                                    href={route("nieuws")} 
                                    className="block px-3 py-2 rounded-md hover:bg-gray-100 font-medium"
                                    onClick={() => setMobileMenuOpen(false)}
                                >
                                    Nieuws
                                </Link>
                                <Link 
                                    href={route("downloads")} 
                                    className="block px-3 py-2 rounded-md hover:bg-gray-100 font-medium"
                                    onClick={() => setMobileMenuOpen(false)}
                                >
                                    Downloads
                                </Link>
                                <Link 
                                    href="/activiteiten" 
                                    className="block px-3 py-2 rounded-md hover:bg-gray-100 font-medium"
                                    onClick={() => setMobileMenuOpen(false)}
                                >
                                    Activiteiten
                                </Link>
                                <Link 
                                    href={route("vereniging")} 
                                    className="block px-3 py-2 rounded-md hover:bg-gray-100 font-medium"
                                    onClick={() => setMobileMenuOpen(false)}
                                >
                                    Leden
                                </Link>
                                <Link 
                                    href={route("organisatie")} 
                                    className="block px-3 py-2 rounded-md hover:bg-gray-100 font-medium"
                                    onClick={() => setMobileMenuOpen(false)}
                                >
                                    Over Ons
                                </Link>
                                <Link 
                                    href={route("regels")} 
                                    className="block px-3 py-2 rounded-md hover:bg-gray-100 font-medium"
                                    onClick={() => setMobileMenuOpen(false)}
                                >
                                    Regels
                                </Link>
                                <Link 
                                    href={route("wedstrijden.index")} 
                                    className="block px-3 py-2 rounded-md hover:bg-gray-100 font-medium"
                                    onClick={() => setMobileMenuOpen(false)}
                                >
                                    Wedstrijden
                                </Link>
                            </nav>

                            {/* Mobile Auth Buttons */}
                            <div className="space-y-2 border-t pt-4">
                                {auth.user ? (
                                    <>
                                        {/* Mobile Notifications */}
                                        <div className="mb-4">
                                            <h4 className="font-medium text-sm text-gray-700 mb-2">Meldingen</h4>
                                            {hasUnreadNotifications && (
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    onClick={markAllAsRead}
                                                    className="text-xs mb-2 w-full justify-start"
                                                >
                                                    Markeer alles als gelezen ({unreadNotifications.length})
                                                </Button>
                                            )}
                                            <div className="max-h-32 overflow-y-auto space-y-1">
                                                {userNotifications.length === 0 ? (
                                                    <p className="text-xs text-gray-500">Geen meldingen</p>
                                                ) : (
                                                    userNotifications.slice(0, 3).map((notification) => (
                                                        <div
                                                            key={notification.id}
                                                            className={`p-2 rounded text-xs ${
                                                                !notification.read_at ? 'bg-blue-50 border-l-2 border-l-blue-500' : 'bg-gray-50'
                                                            }`}
                                                            onClick={() => {
                                                                markAsRead(notification.id);
                                                                setMobileMenuOpen(false);
                                                            }}
                                                        >
                                                            <div className="font-medium truncate">{notification.title}</div>
                                                            <div className="text-gray-600 line-clamp-1">{notification.message}</div>
                                                        </div>
                                                    ))
                                                )}
                                            </div>
                                            {userNotifications.length > 3 && (
                                                <Link
                                                    href="/notifications"
                                                    className="text-xs text-blue-600 hover:text-blue-800 block mt-2"
                                                    onClick={() => setMobileMenuOpen(false)}
                                                >
                                                    Alle meldingen bekijken
                                                </Link>
                                            )}
                                        </div>
                                        
                                        <Link
                                            href={route('dashboard')}
                                            onClick={() => setMobileMenuOpen(false)}
                                        >
                                            <Button className="w-full justify-start">
                                                Dashboard
                                            </Button>
                                        </Link>
                                        {(auth.user.is_admin || (auth.user.roles && auth.user.roles.length > 0)) && (
                                            <Link
                                                href="/admin"
                                                onClick={() => setMobileMenuOpen(false)}
                                            >
                                                <Button variant="outline" className="w-full justify-start border-red-500 text-red-600 hover:bg-red-50 hover:text-red-700">
                                                    Admin
                                                </Button>
                                            </Link>
                                        )}
                                        <Link
                                            href={route('logout')}
                                            method="post"
                                            as="button"
                                            onClick={() => setMobileMenuOpen(false)}
                                        >
                                            <Button variant="outline" className="w-full justify-start border-gray-300 text-gray-600 hover:bg-gray-50 hover:text-gray-700">
                                                Uitloggen
                                            </Button>
                                        </Link>
                                    </>
                                ) : (
                                    <>
                                        <Link
                                            href={route('login')}
                                            onClick={() => setMobileMenuOpen(false)}
                                        >
                                            <Button variant={'secondary'} className="w-full justify-start">
                                                Log in
                                            </Button>
                                        </Link>
                                        <Link
                                            href={route('register')}
                                            onClick={() => setMobileMenuOpen(false)}
                                        >
                                            <Button className="w-full justify-start">
                                                Lid worden
                                            </Button>
                                        </Link>
                                    </>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            )}

            {/* Notification Dropdown */}
            {notificationOpen && auth.user && (
                <div className="fixed inset-0 z-40" onClick={() => setNotificationOpen(false)}>
                    <div 
                        className="absolute top-16 right-4 lg:right-8 w-80 max-w-[90vw] bg-white border border-gray-200 rounded-lg shadow-xl max-h-96 overflow-hidden"
                        onClick={(e) => e.stopPropagation()}
                    >
                        <div className="p-4 border-b border-gray-200 flex justify-between items-center">
                            <h3 className="font-semibold text-lg">Meldingen</h3>
                            {hasUnreadNotifications && (
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    onClick={markAllAsRead}
                                    className="text-xs"
                                >
                                    Alles markeren als gelezen
                                </Button>
                            )}
                        </div>
                        
                        <div className="max-h-80 overflow-y-auto">
                            {userNotifications.length === 0 ? (
                                <div className="p-4 text-center text-gray-500">
                                    Geen meldingen
                                </div>
                            ) : (
                                userNotifications.slice(0, 10).map((notification) => (
                                    <div
                                        key={notification.id}
                                        className={`p-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer ${
                                            !notification.read_at ? 'bg-blue-50 border-l-4 border-l-blue-500' : ''
                                        }`}
                                        onClick={() => markAsRead(notification.id)}
                                    >
                                        <div className="flex justify-between items-start">
                                            <div className="flex-1 min-w-0">
                                                <h4 className="font-medium text-sm text-gray-900 truncate">
                                                    {notification.title}
                                                </h4>
                                                <p className="text-xs text-gray-600 mt-1 line-clamp-2">
                                                    {notification.message}
                                                </p>
                                                <p className="text-xs text-gray-400 mt-1">
                                                    {new Date(notification.created_at).toLocaleDateString('nl-NL', {
                                                        day: 'numeric',
                                                        month: 'short',
                                                        hour: '2-digit',
                                                        minute: '2-digit'
                                                    })}
                                                </p>
                                            </div>
                                            {!notification.read_at && (
                                                <div className="w-2 h-2 bg-blue-500 rounded-full ml-2 flex-shrink-0"></div>
                                            )}
                                        </div>
                                    </div>
                                ))
                            )}
                        </div>
                        
                        {userNotifications.length > 10 && (
                            <div className="p-3 border-t border-gray-200 text-center">
                                <Link
                                    href="/notifications"
                                    className="text-sm text-blue-600 hover:text-blue-800"
                                    onClick={() => setNotificationOpen(false)}
                                >
                                    Alle meldingen bekijken
                                </Link>
                            </div>
                        )}
                    </div>
                </div>
            )}
            
            <DebugPanel />
        </div>
    );
}

const ListItem = React.forwardRef<
  React.ElementRef<"a">,
  React.ComponentPropsWithoutRef<"a">
>(({ className, title, children, ...props }, ref) => {
  return (
    <li>
      <NavigationMenuLink asChild>
        <a
          ref={ref}
          className={cn(
            "block select-none space-y-1 rounded-md p-3 leading-none no-underline outline-none transition-colors hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground cursor-pointer",
            className
          )}
          {...props}
        >
          <div className="text-sm font-medium leading-none">{title}</div>
          <p className="line-clamp-2 text-sm leading-snug text-muted-foreground">
            {children}
          </p>
        </a>
      </NavigationMenuLink>
    </li>
  )
})
ListItem.displayName = "ListItem"