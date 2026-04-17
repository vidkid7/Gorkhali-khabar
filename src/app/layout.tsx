import type { Metadata } from "next";
import { Noto_Sans_Devanagari, Inter } from "next/font/google";
import "./globals.css";
import { Providers } from "@/components/layout/Providers";

const notoSansDevanagari = Noto_Sans_Devanagari({
  variable: "--font-nepali",
  subsets: ["devanagari", "latin"],
  display: "swap",
});

const inter = Inter({
  variable: "--font-latin",
  subsets: ["latin"],
  display: "swap",
});

export const metadata: Metadata = {
  title: {
    default: "समाचार पोर्टल - विश्वसनीय समाचार सेवा",
    template: "%s | समाचार पोर्टल",
  },
  description: "नेपालको विश्वसनीय अनलाइन समाचार पोर्टल",
  keywords: ["news", "nepal", "nepali news", "समाचार", "नेपाल"],
};

// FOUC prevention script - runs before React hydration
const themeScript = `
  (function() {
    try {
      var theme = localStorage.getItem('theme');
      if (!theme) {
        theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
      }
      document.documentElement.dataset.theme = theme;
    } catch(e) {}
  })();
`;

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html
      lang="ne"
      className={`${notoSansDevanagari.variable} ${inter.variable} h-full antialiased`}
      suppressHydrationWarning
    >
      <head>
        <script dangerouslySetInnerHTML={{ __html: themeScript }} />
        <link rel="manifest" href="/manifest.json" />
        <meta name="theme-color" content="#c62828" />
        <link rel="alternate" type="application/rss+xml" title="RSS Feed" href="/rss.xml" />
      </head>
      <body className="min-h-full flex flex-col">
        {/* Animated liquid background blobs */}
        <div className="liquid-bg" aria-hidden="true">
          <div className="liquid-blob liquid-blob-1" />
          <div className="liquid-blob liquid-blob-2" />
          <div className="liquid-blob liquid-blob-3" />
          <div className="liquid-blob liquid-blob-4" />
        </div>
        <Providers>{children}</Providers>
      </body>
    </html>
  );
}
