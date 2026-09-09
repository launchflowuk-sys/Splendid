import type { Metadata } from 'next';
import './globals.css';
export const metadata: Metadata = { title: { default: 'Splendid Double Glazing | A brighter way to live', template: '%s | Splendid Double Glazing' }, description: 'Explore windows, doors and glazed living spaces with Splendid Double Glazing, based on Sidcup Road in South East London.', icons: { icon: '/favicon.svg' } };
export default function RootLayout({children}:Readonly<{children:React.ReactNode}>){return <html lang="en-GB"><body>{children}</body></html>}
