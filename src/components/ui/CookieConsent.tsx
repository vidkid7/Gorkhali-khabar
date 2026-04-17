"use client";

import { useState, useEffect, useCallback } from "react";
import { useLanguage } from "@/contexts/LanguageContext";

interface CookiePreferences {
  essential: boolean;
  analytics: boolean;
  advertising: boolean;
}

const DEFAULT_PREFERENCES: CookiePreferences = {
  essential: true,
  analytics: false,
  advertising: false,
};

export function CookieConsent() {
  const { t } = useLanguage();
  const [visible, setVisible] = useState(false);
  const [showPreferences, setShowPreferences] = useState(false);
  const [preferences, setPreferences] =
    useState<CookiePreferences>(DEFAULT_PREFERENCES);

  useEffect(() => {
    try {
      const consent = localStorage.getItem("cookieConsent");
      if (!consent) {
        setVisible(true);
      }
    } catch {
      // localStorage not available
    }
  }, []);

  const saveConsent = useCallback((prefs: CookiePreferences) => {
    try {
      localStorage.setItem("cookieConsent", JSON.stringify(prefs));
    } catch {
      // localStorage not available
    }
    setVisible(false);
    setShowPreferences(false);
  }, []);

  const acceptAll = () => {
    saveConsent({ essential: true, analytics: true, advertising: true });
  };

  const rejectNonEssential = () => {
    saveConsent({ essential: true, analytics: false, advertising: false });
  };

  const savePreferences = () => {
    saveConsent({ ...preferences, essential: true });
  };

  if (!visible) return null;

  return (
    <>
      {/* Main banner */}
      <div
        role="dialog"
        aria-label="Cookie consent"
        aria-modal={showPreferences}
        className="fixed bottom-0 inset-x-0 z-50 p-4"
      >
        <div className="mx-auto max-w-4xl card p-6 shadow-lg">
          <p className="text-sm text-muted mb-4">
            हामी तपाईंको अनुभव सुधार गर्न कुकीहरू प्रयोग गर्छौं।
            <br />
            We use cookies to improve your experience.{" "}
            <a
              href="/cookie-policy"
              className="text-accent underline hover:no-underline"
            >
              {t("footer.cookiePolicy")}
            </a>
          </p>

          <div className="flex flex-wrap gap-3">
            <button
              onClick={acceptAll}
              className="btn-primary text-sm"
              autoFocus
            >
              Accept All
            </button>
            <button
              onClick={rejectNonEssential}
              className="btn-secondary text-sm"
            >
              Reject Non-Essential
            </button>
            <button
              onClick={() => setShowPreferences(true)}
              className="btn-secondary text-sm"
            >
              Manage Preferences
            </button>
          </div>
        </div>
      </div>

      {/* Preferences modal */}
      {showPreferences && (
        <div
          className="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 p-4"
          role="dialog"
          aria-label="Cookie preferences"
          aria-modal="true"
          onClick={(e) => {
            if (e.target === e.currentTarget) setShowPreferences(false);
          }}
          onKeyDown={(e) => {
            if (e.key === "Escape") setShowPreferences(false);
          }}
        >
          <div className="card p-6 max-w-md w-full space-y-4">
            <h2 className="text-lg font-bold">
              Cookie Preferences / कुकी प्राथमिकताहरू
            </h2>

            {/* Essential - always on */}
            <label className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium">Essential / आवश्यक</p>
                <p className="text-xs text-muted">
                  Required for the site to function
                </p>
              </div>
              <input
                type="checkbox"
                checked
                disabled
                className="w-5 h-5 accent-accent"
                aria-label="Essential cookies (always enabled)"
              />
            </label>

            {/* Analytics */}
            <label className="flex items-center justify-between cursor-pointer">
              <div>
                <p className="text-sm font-medium">Analytics / विश्लेषण</p>
                <p className="text-xs text-muted">
                  Helps us understand site usage
                </p>
              </div>
              <input
                type="checkbox"
                checked={preferences.analytics}
                onChange={(e) =>
                  setPreferences((p) => ({
                    ...p,
                    analytics: e.target.checked,
                  }))
                }
                className="w-5 h-5 accent-accent"
                aria-label="Analytics cookies"
              />
            </label>

            {/* Advertising */}
            <label className="flex items-center justify-between cursor-pointer">
              <div>
                <p className="text-sm font-medium">
                  Advertising / विज्ञापन
                </p>
                <p className="text-xs text-muted">
                  Used for relevant advertisements
                </p>
              </div>
              <input
                type="checkbox"
                checked={preferences.advertising}
                onChange={(e) =>
                  setPreferences((p) => ({
                    ...p,
                    advertising: e.target.checked,
                  }))
                }
                className="w-5 h-5 accent-accent"
                aria-label="Advertising cookies"
              />
            </label>

            <div className="flex gap-3 pt-2">
              <button
                onClick={savePreferences}
                className="btn-primary text-sm flex-1"
              >
                {t("common.save")}
              </button>
              <button
                onClick={() => setShowPreferences(false)}
                className="btn-secondary text-sm flex-1"
              >
                {t("common.cancel")}
              </button>
            </div>
          </div>
        </div>
      )}
    </>
  );
}
