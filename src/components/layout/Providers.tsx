"use client";

import { ThemeProvider } from "@/contexts/ThemeContext";
import { LanguageProvider } from "@/contexts/LanguageContext";
import { SiteConfigProvider } from "@/contexts/SiteConfigContext";
import { FontSizeProvider } from "@/contexts/FontSizeContext";
import { OfflineIndicator } from "@/components/ui/OfflineIndicator";
import { MobileBottomNav } from "@/components/layout/MobileBottomNav";
import { OptionalSessionProvider } from "@/components/layout/OptionalSessionProvider";

export function Providers({ children }: { children: React.ReactNode }) {
  return (
    <OptionalSessionProvider>
      <ThemeProvider>
        <FontSizeProvider>
          <LanguageProvider>
            <SiteConfigProvider>
              <OfflineIndicator />
              {children}
              <MobileBottomNav />
            </SiteConfigProvider>
          </LanguageProvider>
        </FontSizeProvider>
      </ThemeProvider>
    </OptionalSessionProvider>
  );
}
