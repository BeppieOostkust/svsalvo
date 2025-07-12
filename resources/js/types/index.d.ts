import { LucideIcon } from 'lucide-react';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavGroup {
    title: string;
    items: NavItem[];
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon | null;
    isActive?: boolean;
}

export interface SharedData {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    urgentArticles: UrgentArticle[];
    notifications: Notification[];
    ziggy: Config & { location: string };
    [key: string]: unknown;
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    is_admin?: boolean;
    is_active_member?: boolean;
    is_blocked?: boolean;
    roles?: string[];
    [key: string]: unknown; // This allows for additional properties...
}

export interface UrgentArticle {
    id: number;
    title: string;
    excerpt: string;
    slug: string;
    published_at: string;
    author: {
        name: string;
    };
}

export interface Notification {
    id: number;
    type: 'activity' | 'match' | 'nieuws' | 'profile_updated' | 'general';
    title: string;
    message: string;
    data?: {
        [key: string]: any;
        url?: string;
        activity_id?: number;
        match_id?: number;
        article_id?: number;
    };
    read_at: string | null;
    created_at: string;
    updated_at: string;
}

// Organization page interfaces
export interface OrganizationInfo {
    id: number;
    section: string;
    title: string;
    content: string;
    sort_order: number;
    is_active: boolean;
}

export interface BoardMember {
    id: number;
    name: string;
    position: string;
    email?: string;
    phone?: string;
    description?: string;
    avatar?: string;
    avatar_url?: string;
    sort_order: number;
    is_active: boolean;
}

export interface Facility {
    id: number;
    name: string;
    description: string;
    icon_type: string;
    icon_color: string;
    image?: string;
    sort_order: number;
    is_active: boolean;
}

export interface ContactInfo {
    id: number;
    type: 'address' | 'contact' | 'opening_hours' | 'other';
    title: string;
    data: {
        [key: string]: any;
        email?: string;
        phone?: string;
        website?: string;
        street?: string;
        city?: string;
        postal_code?: string;
        country?: string;
        google_maps_url?: string;
        latitude?: number;
        longitude?: number;
        hours?: Array<{day: string; hours: string}>;
    };
    additional_info?: string;
    is_active: boolean;
}

export interface OrganizationPageProps {
    organizationInfo: {
        [key: string]: OrganizationInfo[];
    };
    boardMembers: BoardMember[];
    facilities: Facility[];
    contactInfo: {
        address?: ContactInfo;
        main?: ContactInfo;
        hours?: ContactInfo;
    };
}
