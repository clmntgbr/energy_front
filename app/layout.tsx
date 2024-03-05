import type { Metadata } from 'next'
import { Inter } from 'next/font/google'
import './globals.css'
import { Suspense } from 'react';
import Nav from './nav';

const inter = Inter({ subsets: ['latin'] })

export const metadata: Metadata = {
  title: 'Energy Stations in France',
  description: 'All energy stations in France',
}

export default function RootLayout({
  children,
}: {
  children: React.ReactNode
}) {
  return (
    <html lang="en" className="h-full bg-gray-50">
      <body className="h-full">
        <Suspense>
          <Nav />
        </Suspense>
        {children}
      </body>
    </html>
  )
}
