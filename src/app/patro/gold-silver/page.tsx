"use client";

import { useState, useEffect } from "react";
import { Coins } from "lucide-react";
import { Header } from "@/components/layout/Header";
import { Footer } from "@/components/layout/Footer";
import { PatroTabs } from "@/components/patro/PatroTabs";
import { PublicPageHeader } from "@/components/ui/PublicPageHeader";
import { useLanguage } from "@/contexts/LanguageContext";

type GoldSilverData = {
  gold: { tola_24k: number | null; tola_22k: number | null; gram_24k: number | null };
  silver: { tola: number | null; gram: number | null };
  date: string;
  source: string;
};

export default function GoldSilverPage() {
  const { language } = useLanguage();
  const [data, setData] = useState<GoldSilverData | null>(null);
  const mn = (ne: string, en: string) => language === "ne" ? ne : en;

  useEffect(() => {
    fetch("/api/v1/finance/gold-silver")
      .then(r => r.json())
      .then(d => { if (d.success) setData(d.data); })
      .catch(() => {});
  }, []);

  const fmt = (n: number | null) => n ? `रु. ${n.toLocaleString()}` : "—";

  return (
    <>
      <Header />
      <div className="mx-auto max-w-6xl px-4 py-8" style={{ minHeight: "100vh" }}>
        <PublicPageHeader title={mn("सुन-चाँदी दर", "Gold & Silver Rates")} eyebrow="Finance" description={mn("नेपालको सुन र चाँदीको आजको भाउ", "Today's gold and silver prices in Nepal")} breadcrumbs={[{ label: mn("गृहपृष्ठ", "Home"), href: "/" }, { label: mn("सुन-चाँदी", "Gold & Silver") }]} />

        <PatroTabs />

        {data ? (
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            {/* Gold Card */}
            <div className="utility-panel overflow-hidden"
              style={{ border: "1px solid var(--border)", background: "var(--surface)" }}>
              <div className="border-b border-border px-5 py-4" style={{ background: "var(--surface-alt)", color: "var(--foreground)" }}>
                <h2 className="text-lg font-black flex items-center gap-2" style={{ fontFamily: "var(--font-nepali-serif)" }}><Coins className="h-5 w-5" />{mn("सुन", "Gold")}</h2>
              </div>
              <div className="p-5 space-y-4">
                <div className="flex justify-between items-center">
                  <span className="text-sm" style={{ color: "var(--muted)" }}>{mn("छापावाल सुन (प्रति तोला)", "Fine Gold (per tola)")}</span>
                  <span className="text-xl font-black" style={{ color: "#f59e0b" }}>{fmt(data.gold.tola_24k)}</span>
                </div>
                <div className="flex justify-between items-center">
                  <span className="text-sm" style={{ color: "var(--muted)" }}>{mn("तेजाबी सुन (प्रति तोला)", "Tejabi Gold (per tola)")}</span>
                  <span className="text-lg font-bold" style={{ color: "var(--foreground)" }}>{fmt(data.gold.tola_22k)}</span>
                </div>
                <div className="flex justify-between items-center">
                  <span className="text-sm" style={{ color: "var(--muted)" }}>{mn("प्रति ग्राम", "Per gram")}</span>
                  <span className="text-sm font-semibold" style={{ color: "var(--foreground)" }}>{fmt(data.gold.gram_24k)}</span>
                </div>
              </div>
            </div>

            {/* Silver Card */}
            <div className="utility-panel overflow-hidden"
              style={{ border: "1px solid var(--border)", background: "var(--surface)" }}>
              <div className="border-b border-border px-5 py-4" style={{ background: "var(--surface-alt)", color: "var(--foreground)" }}>
                <h2 className="text-lg font-black flex items-center gap-2" style={{ fontFamily: "var(--font-nepali-serif)" }}><Coins className="h-5 w-5" />{mn("चाँदी", "Silver")}</h2>
              </div>
              <div className="p-5 space-y-4">
                <div className="flex justify-between items-center">
                  <span className="text-sm" style={{ color: "var(--muted)" }}>{mn("चाँदी (प्रति तोला)", "Silver (per tola)")}</span>
                  <span className="text-xl font-black" style={{ color: "#64748b" }}>{fmt(data.silver.tola)}</span>
                </div>
                <div className="flex justify-between items-center">
                  <span className="text-sm" style={{ color: "var(--muted)" }}>{mn("प्रति ग्राम", "Per gram")}</span>
                  <span className="text-sm font-semibold" style={{ color: "var(--foreground)" }}>{fmt(data.silver.gram)}</span>
                </div>
              </div>
            </div>
          </div>
        ) : (
          <div className="text-center py-12" style={{ color: "var(--muted)" }}>
            {mn("लोड हुँदैछ...", "Loading...")}
          </div>
        )}

        {data && (
          <p className="text-xs mt-4 text-center" style={{ color: "var(--muted)" }}>
            {mn("स्रोत", "Source")}: {data.source} • {new Date(data.date).toLocaleDateString(language === "ne" ? "ne-NP" : "en-US")}
          </p>
        )}
      </div>
      <Footer />
    </>
  );
}
