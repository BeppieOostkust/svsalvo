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
    const { auth } = usePage<SharedData>().props;

    return (
        <div>
            <Head title="Welcome">

            </Head>
            <div className='my-4 mx-auto p-4 w-[90%] border-gray-400 shadow-xl rounded-lg flex flex-row justify-between items-center'>
                <div className='flex flex-row items-center gap-4'>
                    <img src="https://placehold.co/32x32" alt="" className='rounded'/>
                    <span className='text-2xl font-bold'>SSV De Moes</span>
                </div>
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
                                    <Link href={route("activiteiten")}>
                                        <NavigationMenuLink className={navigationMenuTriggerStyle()}>
                                        Activiteiten
                                        </NavigationMenuLink>
                                    </Link>
                                </NavigationMenuItem>
                                <NavigationMenuItem>
                                    <Link href={route("organisatie")}>
                                        <NavigationMenuLink className={navigationMenuTriggerStyle()}>
                                        Organisatie
                                        </NavigationMenuLink>
                                    </Link>
                                </NavigationMenuItem>
                                <NavigationMenuItem>
                                    <NavigationMenuTrigger>Wedstrijden</NavigationMenuTrigger>
                                    <NavigationMenuContent>
                                        <ul className="grid w-[400px] gap-3 p-4 md:w-[500px] md:grid-cols-2 lg:w-[600px]">
                                            <li className="row-span-5">
                                                <NavigationMenuLink asChild>
                                                    <a
                                                        className="flex h-full w-full select-none flex-col justify-end rounded-md bg-gradient-to-b from-blue-300 to-blue-500 p-6 no-underline outline-none focus:shadow-md cursor-pointer"
                                                        href='https://youtube.com'
                                                    >
                                                        <div className="mb-2 mt-4 text-lg font-medium text-white">
                                                            Wedstrijden
                                                        </div>
                                                        <p className="text-sm leading-tight text-foreground">
                                                            Beautifully designed components built with Radix UI and
                                                            Tailwind CSS.
                                                        </p>
                                                    </a>
                                                </NavigationMenuLink>
                                            </li>
                                            {wedstrijden.map((component) => (
                                                <ListItem
                                                    key={component.naam}
                                                    title={component.naam}
                                                    href={route("nieuws")}
                                                >
                                                    {component.beschrijving}
                                                </ListItem>
                                            ))}
                                        </ul>
                                    </NavigationMenuContent>
                                </NavigationMenuItem>
                                <NavigationMenuItem>
                                    <Link href={route("instellingen")}>
                                        <NavigationMenuLink className={navigationMenuTriggerStyle()}>
                                        Instellingen
                                        </NavigationMenuLink>
                                    </Link>
                                </NavigationMenuItem>
                            </NavigationMenuList>
                        </NavigationMenu>
                    </nav>
                </div>
                {auth.user ? (
                    <Link
                        href={route('dashboard')}
                    >
                        <Button>Dashboard</Button>
                        
                    </Link>
                ) : (
                    <>
                        <Link
                            href={route('login')}
                        >
                            <Button variant={'secondary'}>Log in</Button>
                            
                        </Link>
                    </>
                )}
            </div>
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