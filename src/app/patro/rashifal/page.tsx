"use client";

import { useState, useEffect } from "react";
import { Header } from "@/components/layout/Header";
import { Footer } from "@/components/layout/Footer";
import { PatroTabs } from "@/components/patro/PatroTabs";
import { useLanguage } from "@/contexts/LanguageContext";

type RashifalEntry = {
  sign: string; sign_ne?: string | null;
  prediction: string; prediction_en?: string | null;
  rating?: number | null;
};

const RASHI_EMOJIS: Record<string, string> = {
  mesh: "♈", brish: "♉", mithun: "♊", karkat: "♋",
  simha: "♌", kanya: "♍", tula: "♎", brishchik: "♏",
  dhanu: "♐", makar: "♑", kumbha: "♒", meen: "♓",
};

export default function RashifalPage() {
  const { language } = useLanguage();
  const [entries, setEntries] = useState<RashifalEntry[]>([]);
  const [selectedSign, setSelectedSign] = useState<string | null>(null);
  const mn = (ne: string, en: string) => language === "ne" ? ne : en;

  useEffect(() => {
    fetch("/api/v1/rashifal")
      .then(r => r.json())
      .then(d => { if (d.success) setEntries(d.data); })
      .catch(() => {});
  }, []);

  const selected = selectedSign ? entries.find(e => e.sign === selectedSign) : null;

  return (
    <>
      <Header />
      <div className="mx-auto max-w-6xl px-4 py-8" style={{ minHeight: "100vh" }}>
        <div className="flex items-center gap-3 mb-6">
          <div className="w-10 h-10 rounded-xl flex items-center justify-center text-white text-lg"
            style={{ background: "linear-gradient(135deg,#7c3aed,#6d28d9)" }}>♈</div>
          <div>
            <h1 className="text-2xl font-black" style={{ color: "var(--foreground)" }}>
              {mn("आजको राशिफल", "Today's Horoscope")}
            </h1>
            <p className="text-xs mt-0.5" style={{ color: "var(--muted)" }}>
              {mn("दैनिक राशिफल — तपाईंको राशि छान्नुहोस्", "Daily horoscope — select your zodiac sign")}
            </p>
          </div>
        </div>

        <PatroTabs />

        {/* Sign selector grid */}
        <div className="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3 mb-6">
          {entries.map(e => (
            <button key={e.sign} onClick={() => setSelectedSign(e.sign)}
              className="flex flex-col items-center gap-1 p-3 rounded-xl transition-all active:scale-95"
              style={{
                background: selectedSign === e.sign ? "linear-gradient(135deg,#7c3aed,#6d28d9)" : "var(--surface)",
                color: selectedSign === e.sign ? "#fff" : "var(--foreground)",
                border: `1px solid ${selectedSign === e.sign ? "#7c3aed" : "var(--border)"}`,
              }}>
              <span className="text-2xl">{RASHI_EMOJIS[e.sign] ?? "⭐"}</span>
              <span className="text-xs font-semibold">{language === "ne" ? e.sign_ne : e.sign}</span>
            </button>
          ))}
        </div>

        {/* Selected sign prediction */}
        {selected && (
          <div className="rounded-2xl overflow-hidden animate-fadeIn"
            style={{ border: "1px solid var(--border)", background: "var(--surface)" }}>
            <div className="px-5 py-4 flex items-center gap-3"
              style={{ background: "linear-gradient(135deg,#7c3aed,#6d28d9)", color: "#fff" }}>
              <span className="text-3xl">{RASHI_EMOJIS[selected.sign] ?? "⭐"}</span>
              <div>
                <h2 className="text-lg font-black">{language === "ne" ? selected.sign_ne : selected.sign}</h2>
                {selected.rating && (
                  <div className="flex items-center gap-0.5 mt-0.5">
                    {Array.from({ length: 5 }, (_, i) => (
                      <span key={i} className={`text-sm ${i < selected.rating! ? "opacity-100" : "opacity-30"}`}>⭐</span>
                    ))}
                  </div>
                )}
              </div>
            </div>
            <div className="p-5">
              <p className="text-sm leading-relaxed" style={{ color: "var(--foreground)" }}>
                {language === "en" && selected.prediction_en ? selected.prediction_en : selected.prediction}
              </p>
            </div>
          </div>
        )}

        {!entries.length && (
          <div className="text-center py-12" style={{ color: "var(--muted)" }}>
            {mn("लोड हुँदैछ...", "Loading...")}
          </div>
        )}
      </div>
      <Footer />
    </>
  );
}
