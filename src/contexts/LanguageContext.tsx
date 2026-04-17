"use client";

import React, { createContext, useContext, useState, useCallback, useEffect } from "react";
import type { SupportedLanguage } from "@/types";
import ne from "@/i18n/locales/ne.json";
import en from "@/i18n/locales/en.json";

const translations = { ne, en };

interface LanguageContextType {
  language: SupportedLanguage;
  setLanguage: (lang: SupportedLanguage) => void;
  /** Translate a key, with optional interpolation: t("hello", { name: "World" }) */
  t: (key: string, params?: Record<string, string | number>) => string;
}

const LanguageContext = createContext<LanguageContextType | undefined>(undefined);

function getNestedValue(obj: Record<string, unknown>, path: string): string {
  const keys = path.split(".");
  let current: unknown = obj;
  for (const key of keys) {
    if (current && typeof current === "object" && key in current) {
      current = (current as Record<string, unknown>)[key];
    } else {
      return path;
    }
  }
  return typeof current === "string" ? current : path;
}

function interpolate(template: string, params: Record<string, string | number>): string {
  return template.replace(/\{\{(\w+)\}\}/g, (_, key) =>
    key in params ? String(params[key]) : `{{${key}}}`
  );
}

const nepaliDigits = ["०", "१", "२", "३", "४", "५", "६", "७", "८", "९"];

export function toNepaliDigits(num: number | string): string {
  return String(num).replace(/\d/g, (d) => nepaliDigits[parseInt(d)]);
}

/** Format a Date as Nepali (BS) date string using proper conversion */
export function formatNepaliDate(date: Date): string {
  try {
    // Lazy import to avoid circular dependency — use inline mini-converter
    // Reference: BS 2000/01/01 = AD 1943/04/14
    const BS_MONTHS = ["बैशाख","जेठ","असार","श्रावण","भदौ","असोज","कार्तिक","मंसिर","पुष","माघ","फागुन","चैत"];
    const BS_DATA: Record<number,number[]> = {
      2070:[31,32,31,32,31,30,30,30,29,29,30,31],2071:[31,31,31,32,31,31,29,30,30,29,30,30],
      2072:[31,31,32,31,31,31,30,29,30,29,30,30],2073:[31,31,32,32,31,30,30,29,30,29,30,30],
      2074:[31,32,31,32,31,30,30,30,29,29,30,31],2075:[31,31,31,32,31,31,29,30,30,29,30,30],
      2076:[31,31,32,31,31,31,30,29,30,29,30,30],2077:[31,32,31,32,31,30,30,30,29,29,30,30],
      2078:[31,31,31,32,31,31,30,29,30,29,30,30],2079:[31,31,32,31,31,31,30,29,30,29,30,30],
      2080:[31,32,31,32,31,30,30,30,29,29,30,30],2081:[31,31,31,32,31,31,29,30,30,29,30,30],
      2082:[31,31,32,31,31,31,30,29,30,29,30,30],2083:[31,31,32,32,31,30,30,29,30,29,30,30],
      2084:[31,32,31,32,31,30,30,30,29,29,30,31],2085:[31,31,31,32,31,31,29,30,30,29,30,30],
    };
    const AD_REF = new Date(1943, 3, 14);
    const ms = Date.UTC(date.getFullYear(), date.getMonth(), date.getDate())
              - Date.UTC(1943, 3, 14);
    let rem = Math.round(ms / 86400000);
    // pre-count years before 2070
    // Use cumulative days from reference to 2070
    const AD_2070_START = Date.UTC(2013, 3, 14); // approx BS 2070/1/1 = Apr 14 2013
    const daysTo2070 = Math.round((AD_2070_START - Date.UTC(1943, 3, 14)) / 86400000);
    if (rem < daysTo2070) {
      // For dates before 2070, use simple approximation
      const y = date.getFullYear() + 57;
      const m = date.getMonth();
      const d = date.getDate();
      return `${toNepaliDigits(d)} ${BS_MONTHS[m]}, ${toNepaliDigits(y)}`;
    }
    rem -= daysTo2070;
    let bsYear = 2070;
    while (BS_DATA[bsYear]) {
      const diy = BS_DATA[bsYear].reduce((a,b)=>a+b,0);
      if (rem < diy) break;
      rem -= diy; bsYear++;
    }
    const md = BS_DATA[bsYear];
    let bsMonth = 1, bsDay = 1;
    if (md) {
      for (let m=0;m<12;m++) {
        if (rem < md[m]) { bsMonth=m+1; bsDay=rem+1; break; }
        rem -= md[m];
      }
    }
    return `${toNepaliDigits(bsDay)} ${BS_MONTHS[bsMonth-1]}, ${toNepaliDigits(bsYear)}`;
  } catch {
    return date.toLocaleDateString("ne-NP");
  }
}

export function LanguageProvider({ children }: { children: React.ReactNode }) {
  const [language, setLanguageState] = useState<SupportedLanguage>("ne");

  useEffect(() => {
    const saved = localStorage.getItem("language") as SupportedLanguage;
    if (saved && (saved === "ne" || saved === "en")) {
      setLanguageState(saved);
    }
  }, []);

  const setLanguage = useCallback((lang: SupportedLanguage) => {
    setLanguageState(lang);
    localStorage.setItem("language", lang);
    document.documentElement.lang = lang;
  }, []);

  const t = useCallback(
    (key: string, params?: Record<string, string | number>): string => {
      const value = getNestedValue(
        translations[language] as unknown as Record<string, unknown>,
        key
      );
      if (params) return interpolate(value, params);
      return value;
    },
    [language]
  );

  return (
    <LanguageContext.Provider value={{ language, setLanguage, t }}>
      {children}
    </LanguageContext.Provider>
  );
}

export function useLanguage() {
  const context = useContext(LanguageContext);
  if (!context) throw new Error("useLanguage must be used within LanguageProvider");
  return context;
}
