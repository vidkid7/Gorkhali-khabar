"use client";

import { useEffect, useState } from "react";
import { BrandWordmark } from "@/components/layout/BrandWordmark";

interface LoadingScreenProps {
  /**
   * If true, the loader acts as a fixed splash screen that auto-hides once the
   * application has finished hydrating. If false, it renders as a plain centered
   * loader suitable for `loading.tsx`.
   */
  splash?: boolean;
  /**
   * Minimum time (ms) the splash screen stays visible so users aren't greeted by a flash.
   */
  minDisplayMs?: number;
  /** Optional message shown under the brand name. */
  message?: string;
}

export function LoadingScreen({
  splash = false,
  minDisplayMs = 0,
  message = "सत्य, सन्तुलित र समयमै",
}: LoadingScreenProps) {
  const [visible, setVisible] = useState(true);

  useEffect(() => {
    if (!splash) return;
    const hideTimer = setTimeout(() => setVisible(false), minDisplayMs);
    return () => clearTimeout(hideTimer);
  }, [splash, minDisplayMs]);

  const content = (
    <div className="fixed inset-0 z-[9999] flex items-center justify-center bg-[var(--background)] px-6">
      <div className="flex w-full max-w-md flex-col items-center gap-6 text-center">
        <BrandWordmark compact priority className="w-full max-w-[420px]" />

        <p className="text-sm text-muted-foreground md:text-base" role="status" aria-live="polite">
          {message}
        </p>

        <div className="editorial-band w-32" aria-hidden="true">
          <span className="h-full w-1/2 bg-[var(--primary)]" />
          <span className="h-full w-1/2 bg-[var(--accent)]" />
        </div>

        <span className="sr-only" role="status" aria-live="polite">
          पृष्ठ लोड हुँदैछ
        </span>
      </div>
    </div>
  );

  if (!splash) return content;

  return (
    <div
      className={`transition-opacity duration-300 ease-out ${
        visible ? "opacity-100" : "pointer-events-none opacity-0"
      }`}
      aria-hidden={!visible}
    >
      {content}
    </div>
  );
}
