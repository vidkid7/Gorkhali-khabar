"use client";

import { useState } from "react";
import { ArrowRight } from "lucide-react";
import { Header } from "@/components/layout/Header";
import { Footer } from "@/components/layout/Footer";
import { PatroTabs } from "@/components/patro/PatroTabs";
import { PublicPageHeader } from "@/components/ui/PublicPageHeader";
import { useLanguage } from "@/contexts/LanguageContext";
import {
  bsToAD, adToBS,
  BS_MONTHS_NE, BS_MONTHS_EN, DAYS_FULL_NE, DAYS_FULL_EN,
  formatADDate, toNepaliNums,
} from "@/lib/nepali-date";

export default function DateConverterPage() {
  const { language } = useLanguage();
  const mn = (ne: string, en: string) => language === "ne" ? ne : en;

  const [bsYear, setBsYear] = useState(2082);
  const [bsMonth, setBsMonth] = useState(1);
  const [bsDay, setBsDay] = useState(1);
  const [adYear, setAdYear] = useState(2025);
  const [adMonth, setAdMonth] = useState(1);
  const [adDay, setAdDay] = useState(1);
  const [resultBS, setResultBS] = useState<string | null>(null);
  const [resultAD, setResultAD] = useState<string | null>(null);

  function convertBStoAD() {
    try {
      const ad = bsToAD(bsYear, bsMonth, bsDay);
      const dow = language === "ne" ? DAYS_FULL_NE[ad.getDay()] : DAYS_FULL_EN[ad.getDay()];
      setResultAD(`${dow}, ${formatADDate(ad, language)}`);
    } catch {
      setResultAD(mn("अमान्य मिति", "Invalid date"));
    }
  }

  function convertADtoBS() {
    try {
      const bs = adToBS(new Date(adYear, adMonth - 1, adDay));
      const monthName = language === "ne" ? BS_MONTHS_NE[bs.month - 1] : BS_MONTHS_EN[bs.month - 1];
      setResultBS(language === "ne"
        ? `${toNepaliNums(bs.day)} ${monthName}, ${toNepaliNums(bs.year)}`
        : `${bs.day} ${monthName}, ${bs.year}`);
    } catch {
      setResultBS(mn("अमान्य मिति", "Invalid date"));
    }
  }

  const selectStyle = {
    background: "var(--surface)",
    color: "var(--foreground)",
    borderColor: "var(--border)",
  };

  return (
    <>
      <Header />
      <div className="mx-auto max-w-6xl px-4 py-8" style={{ minHeight: "100vh" }}>
        <PublicPageHeader title={mn("मिति परिवर्तन", "Date Converter")} eyebrow="Calendar" description={mn("बि.सं. र ई.सं. मिति रूपान्तरण", "Convert between BS and AD dates")} breadcrumbs={[{ label: mn("गृहपृष्ठ", "Home"), href: "/" }, { label: mn("मिति परिवर्तन", "Date Converter") }]} />

        <PatroTabs />

        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          {/* BS to AD */}
          <div className="utility-panel overflow-hidden"
            style={{ border: "1px solid var(--border)", background: "var(--surface)" }}>
            <div className="border-b border-border px-5 py-4" style={{ background: "var(--surface-alt)", color: "var(--foreground)" }}>
              <h2 className="text-sm font-bold flex items-center gap-1.5" style={{ fontFamily: "var(--font-nepali-serif)" }}>{mn("बि.सं.", "BS")} <ArrowRight className="h-3.5 w-3.5" /> {mn("ई.सं.", "AD")}</h2>
            </div>
            <div className="p-5 space-y-4">
              <div className="grid grid-cols-3 gap-2 sm:gap-3">
                <div>
                  <label className="text-xs font-semibold mb-1 block" style={{ color: "var(--muted)" }}>{mn("वर्ष", "Year")}</label>
                  <select value={bsYear} onChange={e => setBsYear(+e.target.value)}
                    className="w-full text-sm px-2 py-2 rounded-lg border" style={selectStyle}>
                    {Array.from({ length: 20 }, (_, i) => 2070 + i).map(y => (
                      <option key={y} value={y}>{language === "ne" ? toNepaliNums(y) : y}</option>
                    ))}
                  </select>
                </div>
                <div>
                  <label className="text-xs font-semibold mb-1 block" style={{ color: "var(--muted)" }}>{mn("महिना", "Month")}</label>
                  <select value={bsMonth} onChange={e => setBsMonth(+e.target.value)}
                    className="w-full text-sm px-2 py-2 rounded-lg border" style={selectStyle}>
                    {BS_MONTHS_NE.map((m, i) => (
                      <option key={i} value={i + 1}>{language === "ne" ? m : BS_MONTHS_EN[i]}</option>
                    ))}
                  </select>
                </div>
                <div>
                  <label className="text-xs font-semibold mb-1 block" style={{ color: "var(--muted)" }}>{mn("दिन", "Day")}</label>
                  <select value={bsDay} onChange={e => setBsDay(+e.target.value)}
                    className="w-full text-sm px-2 py-2 rounded-lg border" style={selectStyle}>
                    {Array.from({ length: 32 }, (_, i) => i + 1).map(d => (
                      <option key={d} value={d}>{language === "ne" ? toNepaliNums(d) : d}</option>
                    ))}
                  </select>
                </div>
              </div>
              <button onClick={convertBStoAD}
                className="w-full py-2.5 rounded-lg text-sm font-bold text-white transition-all active:scale-95"
                style={{ background: "var(--accent)" }}>
                {mn("रूपान्तरण गर्नुहोस्", "Convert")}
              </button>
              {resultAD && (
                <div className="p-3 rounded-lg text-center" style={{ background: "var(--accent-light)" }}>
                  <p className="text-xs" style={{ color: "var(--muted)" }}>{mn("ई.सं. मिति", "AD Date")}</p>
                  <p className="text-lg font-bold mt-1" style={{ color: "var(--foreground)" }}>{resultAD}</p>
                </div>
              )}
            </div>
          </div>

          {/* AD to BS */}
          <div className="utility-panel overflow-hidden"
            style={{ border: "1px solid var(--border)", background: "var(--surface)" }}>
            <div className="border-b border-border px-5 py-4" style={{ background: "var(--surface-alt)", color: "var(--foreground)" }}>
              <h2 className="text-sm font-bold flex items-center gap-1.5" style={{ fontFamily: "var(--font-nepali-serif)" }}>{mn("ई.सं.", "AD")} <ArrowRight className="h-3.5 w-3.5" /> {mn("बि.सं.", "BS")}</h2>
            </div>
            <div className="p-5 space-y-4">
              <div className="grid grid-cols-3 gap-2 sm:gap-3">
                <div>
                  <label className="text-xs font-semibold mb-1 block" style={{ color: "var(--muted)" }}>{mn("वर्ष", "Year")}</label>
                  <select value={adYear} onChange={e => setAdYear(+e.target.value)}
                    className="w-full text-sm px-2 py-2 rounded-lg border" style={selectStyle}>
                    {Array.from({ length: 20 }, (_, i) => 2015 + i).map(y => (
                      <option key={y} value={y}>{y}</option>
                    ))}
                  </select>
                </div>
                <div>
                  <label className="text-xs font-semibold mb-1 block" style={{ color: "var(--muted)" }}>{mn("महिना", "Month")}</label>
                  <select value={adMonth} onChange={e => setAdMonth(+e.target.value)}
                    className="w-full text-sm px-2 py-2 rounded-lg border" style={selectStyle}>
                    {["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"].map((m, i) => (
                      <option key={i} value={i + 1}>{m}</option>
                    ))}
                  </select>
                </div>
                <div>
                  <label className="text-xs font-semibold mb-1 block" style={{ color: "var(--muted)" }}>{mn("दिन", "Day")}</label>
                  <select value={adDay} onChange={e => setAdDay(+e.target.value)}
                    className="w-full text-sm px-2 py-2 rounded-lg border" style={selectStyle}>
                    {Array.from({ length: 31 }, (_, i) => i + 1).map(d => (
                      <option key={d} value={d}>{d}</option>
                    ))}
                  </select>
                </div>
              </div>
              <button onClick={convertADtoBS}
                className="w-full py-2.5 rounded-lg text-sm font-bold text-white transition-all active:scale-95"
                style={{ background: "var(--primary)" }}>
                {mn("रूपान्तरण गर्नुहोस्", "Convert")}
              </button>
              {resultBS && (
                <div className="p-3 rounded-lg text-center" style={{ background: "var(--accent-light)" }}>
                  <p className="text-xs" style={{ color: "var(--muted)" }}>{mn("बि.सं. मिति", "BS Date")}</p>
                  <p className="text-lg font-bold mt-1" style={{ color: "var(--foreground)" }}>{resultBS}</p>
                </div>
              )}
            </div>
          </div>
        </div>
      </div>
      <Footer />
    </>
  );
}
