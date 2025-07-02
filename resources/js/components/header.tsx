import React from 'react';
import { type SharedData } from '@/types';
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


const wedstrijden: {naam: string; href: any; beschrijving: string}[] = [
    {
        naam: "Service Pistool",
        href: route("nieuws"),
        beschrijving: "Schieten"
    },
    {
        naam: "Test",
        href: route("nieuws") || "#",
        beschrijving: "Testen",
    },
    {
        naam: "Kleinkaliber Geweer",
        href: route("nieuws") || "#",
        beschrijving: "Precisieschieten met kleinkaliber geweer",
    },
    {
        naam: "Luchtgeweer",
        href: route("nieuws") || "#",
        beschrijving: "Schieten met luchtgeweer",
    },
    {
        naam: "Boogschieten",
        href: route("nieuws") || "#",
        beschrijving: "Schieten met boog",
    }
];

export default function Header() {
    const { auth, urgentArticles } = usePage<SharedData>().props;
    const [bannerDismissed, setBannerDismissed] = React.useState(false);
    
    // Check if we have urgent articles to display
    const hasUrgentArticles = urgentArticles && Array.isArray(urgentArticles) && urgentArticles.length > 0;
    
    // Check if any urgent articles are dismissed
    const [dismissedArticleIds, setDismissedArticleIds] = React.useState<number[]>([]);
    const visibleUrgentArticles = hasUrgentArticles 
        ? urgentArticles.filter(article => !dismissedArticleIds.includes(article.id))
        : [];
    const hasVisibleUrgentArticles = visibleUrgentArticles.length > 0;
    
    const shouldShowBanner = hasVisibleUrgentArticles && !bannerDismissed;

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
            <Head title="Welcome">

            </Head>
            
            {/* Urgent Articles Banner */}
            {shouldShowBanner && (
                <UrgentArticles urgentArticles={urgentArticles} />
            )}
            
            {/* Main Header - add top padding when urgent banner is present and not dismissed */}
            <div className={`my-4 mx-auto p-4 w-[90%] border-gray-400 shadow-xl rounded-lg flex flex-row justify-between items-center transition-all duration-500 ease-in-out ${shouldShowBanner ? 'mt-20' : 'mt-4'}`}>
                <Link href={route("dashboard.home")} className='flex flex-row items-center gap-4 hover:opacity-80 transition-opacity cursor-pointer'>
                    <img src="https://placehold.co/32x32" alt="" className='rounded'/>
                    <span className='text-2xl font-bold'>SSV De Moes</span>
                </Link>
                <div>
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
                {auth.user ? (
                    <div className="flex gap-2">
                        <Link
                            href={route('dashboard')}
                        >
                            <Button>Dashboard</Button>
                        </Link>
                        {(auth.user.is_admin || (auth.user.roles && auth.user.roles.length > 0)) && (
                            <Link
                                href="/admin"
                            >
                                <Button variant="outline" className="border-red-500 text-red-600 hover:bg-red-50 hover:text-red-700">
                                    Admin
                                </Button>
                            </Link>
                        )}
                    </div>
                ) : (
                    <div className="flex gap-2">
                        <Link
                            href={route('login')}
                        >
                            <Button variant={'secondary'}>Log in</Button>
                        </Link>
                        <Link
                            href={route('register')}
                        >
                            <Button>Lid worden</Button>
                        </Link>
                    </div>
                )}
            </div>
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