import { type ClaSValue, clsx } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClaSValue[]) {
    return twMerge(clsx(inputs));
}
