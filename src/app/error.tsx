"use client";

import { useEffect } from "react";
import Link from "next/link";
import { useLanguage } from "@/contexts/LanguageContext";
import { AlertTriangle } from "lucide-react";

export default function Error({
  error,
  reset,
}: {
  error: Error & { digest?: string };
  reset: () => void;
}) {
  const { t } = useLanguage();
  
  useEffect(() => {
    console.error(error);
  }, [error]);

  return (
    <div className="min-h-screen bg-background px-4 py-12">
      <div className="utility-panel mx-auto w-full max-w-lg p-8 text-center">
        <div className="mx-auto mb-6 flex h-16 w-16 items-center justify-center border-b-4 border-primary bg-surface-alt text-primary">
          <AlertTriangle className="h-8 w-8" />
        </div>

        <h1 className="text-2xl font-bold text-foreground mb-3" style={{ fontFamily: "var(--font-nepali-serif)" }}>
          {t("errors.serverError")}
        </h1>
        <p className="text-muted mb-3 leading-relaxed">
          {t("errors.serverErrorDesc")}
        </p>
        {error.digest && (
          <p className="text-xs text-muted mb-8 font-mono bg-surface border border-border rounded px-3 py-1.5 inline-block">
            {t("errors.errorId")} {error.digest}
          </p>
        )}
        {!error.digest && <div className="mb-8" />}

        <div className="flex flex-wrap justify-center gap-3">
          <button onClick={() => reset()} className="btn-primary">
            {t("errors.tryAgain")}
          </button>
          <Link href="/" className="btn-secondary">
            {t("errors.goHome")}
          </Link>
        </div>
      </div>
    </div>
  );
}
