"use client";

import { useLanguage } from "@/contexts/LanguageContext";
import { toNepaliDigits } from "@/contexts/LanguageContext";
import { useState, useEffect, useCallback } from "react";
import { Header } from "@/components/layout/Header";
import { Footer } from "@/components/layout/Footer";

interface StockData {
  symbol: string;
  name: string;
  price: number;
  change: number;
  pct: number;
  volume: number;
  high: number;
  low: number;
}

interface IndexData {
  name: string;
  nameNe: string;
  value: number;
  change: number;
  pct: number;
}

function isMarketOpen(): boolean {
  // NEPSE: Sunday–Thursday, 11:00–15:00 Nepal time (UTC+5:45)
  const now = new Date();
  const utcMs = now.getTime() + now.getTimezoneOffset() * 60000;
  const nepalMs = utcMs + 5 * 3600000 + 45 * 60000;
  const nepal = new Date(nepalMs);
  const day = nepal.getDay(); // 0=Sun, 1=Mon ... 4=Thu, 5=Fri, 6=Sat
  const hour = nepal.getHours();
  const min = nepal.getMinutes();
  const timeOk = (hour > 11 || (hour === 11 && min >= 0)) && hour < 15;
  return (day >= 0 && day <= 4) && timeOk;
}

function generateData(): { stocks: StockData[]; indices: IndexData[] } {
  const base = [
    { symbol: "NABIL",  name: "नबिल बैंक",            basePrice: 1245, vol: 12450 },
    { symbol: "NLIC",   name: "नेपाल लाइफ इन्स्योरेन्स", basePrice: 1024, vol: 8920  },
    { symbol: "SCB",    name: "स्ट्यान्डर्ड चार्टर्ड बैंक", basePrice: 876,  vol: 3450  },
    { symbol: "SBI",    name: "नेपाल एसबीआई बैंक",     basePrice: 456,  vol: 15680 },
    { symbol: "HBL",    name: "हिमालयन बैंक",          basePrice: 1478, vol: 9870  },
    { symbol: "GBIME",  name: "ग्लोबल आइएमई बैंक",     basePrice: 342,  vol: 21340 },
    { symbol: "NTC",    name: "नेपाल टेलिकम",          basePrice: 789,  vol: 4560  },
    { symbol: "NIFRA",  name: "नेपाल इन्फ्रास्ट्रक्चर", basePrice: 567,  vol: 7890  },
    { symbol: "SHIVM",  name: "शिवम् सिमेन्ट्स",       basePrice: 445,  vol: 6780  },
    { symbol: "CHDC",   name: "चिल्ड्रेन डेभलपमेन्ट",  basePrice: 456,  vol: 5670  },
  ];

  const stocks = base.map((s) => {
    const v = (Math.random() - 0.5) * 0.06;
    const change = +(s.basePrice * v).toFixed(2);
    const price = +(s.basePrice + change).toFixed(2);
    return {
      symbol: s.symbol, name: s.name, price, change,
      pct: +((change / s.basePrice) * 100).toFixed(2),
      volume: Math.floor(s.vol * (0.8 + Math.random() * 0.4)),
      high: +(price * (1 + Math.random() * 0.02)).toFixed(2),
      low:  +(price * (1 - Math.random() * 0.02)).toFixed(2),
    };
  });

  const nepseBase = 2456.78;
  const nc = +(nepseBase * (Math.random() - 0.5) * 0.03).toFixed(2);
  const indices: IndexData[] = [
    { name: "NEPSE",           nameNe: "नेप्से",        value: +(nepseBase + nc).toFixed(2), change: nc, pct: +((nc/nepseBase)*100).toFixed(2) },
    { name: "Sensitive",       nameNe: "सेन्सेटिभ",     value: +(467.89 + (Math.random()-0.5)*12).toFixed(2), change: +((Math.random()-0.5)*12).toFixed(2), pct: +((Math.random()-0.5)*3).toFixed(2) },
    { name: "Float",           nameNe: "फ्लोट",         value: +(198.34 + (Math.random()-0.5)*6).toFixed(2),  change: +((Math.random()-0.5)*6).toFixed(2),  pct: +((Math.random()-0.5)*3).toFixed(2) },
    { name: "Sensitive Float", nameNe: "सेन्सेटिभ फ्लोट", value: +(134.56 + (Math.random()-0.5)*4).toFixed(2), change: +((Math.random()-0.5)*4).toFixed(2), pct: +((Math.random()-0.5)*3).toFixed(2) },
  ];

  return { stocks, indices };
}

const SECTORS = ["सबै", "बैंकिङ", "बीमा", "हाइड्रो", "सिमेन्ट", "टेलिकम"] as const;

export default function ShareMarketPage() {
  const { language } = useLanguage();
  const [data, setData] = useState<ReturnType<typeof generateData> | null>(null);
  const [lastUpdate, setLastUpdate] = useState<Date | null>(null);
  const [sector, setSector] = useState("सबै");
  const [countdown, setCountdown] = useState(60);
  const [marketOpen] = useState(isMarketOpen);
  const [isLive, setIsLive] = useState(false);

  const refresh = useCallback(async () => {
    try {
      const res = await fetch("/api/v1/nepse");
      if (res.ok) {
        const json = await res.json();
        setData({ stocks: json.stocks, indices: json.indices });
        setIsLive(json.live === true);
      } else {
        setData(generateData());
        setIsLive(false);
      }
    } catch {
      setData(generateData());
      setIsLive(false);
    }
    setLastUpdate(new Date());
    setCountdown(60);
  }, []);

  useEffect(() => { refresh(); }, [refresh]);

  // Auto-refresh every 60 seconds
  useEffect(() => {
    const timer = setInterval(refresh, 60000);
    return () => clearInterval(timer);
  }, [refresh]);

  // Countdown ticker
  useEffect(() => {
    const tick = setInterval(() => setCountdown((c) => (c > 0 ? c - 1 : 60)), 1000);
    return () => clearInterval(tick);
  }, []);

  const ne = (nepali: string, english: string) => language === "ne" ? nepali : english;
  const fmt = (n: number) => language === "ne" ? toNepaliDigits(n.toLocaleString()) : n.toLocaleString();

  if (!data) {
    return (
      <div className="min-h-screen flex items-center justify-center" style={{ background: "var(--background)" }}>
        <div className="animate-spin w-8 h-8 border-4 border-primary border-t-transparent rounded-full" style={{ borderColor: "var(--primary)", borderTopColor: "transparent" }} />
      </div>
    );
  }

  return (
    <>
      <Header />
      <main className="min-h-screen" style={{ background: "var(--background)" }}>
        <div className="mx-auto max-w-6xl px-4 py-8">

          {/* Header Row */}
          <div className="flex flex-wrap items-start justify-between gap-4 mb-6">
            <div>
              <h1 className="text-2xl font-bold flex items-center gap-2" style={{ color: "var(--foreground)" }}>
                📊 {ne("सेयर बजार (NEPSE)", "Share Market (NEPSE)")}
              </h1>
              <p className="text-sm mt-1" style={{ color: "var(--muted)" }}>
                {ne("नेपाल स्टक एक्सचेन्ज — बजार डाटा", "Nepal Stock Exchange — Market Data")}
              </p>
            </div>
            <div className="flex items-center gap-3">
              {/* Market status */}
              <span className="flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full"
                style={{ background: marketOpen ? "rgba(22,163,74,0.15)" : "rgba(220,38,38,0.12)",
                         color: marketOpen ? "#16a34a" : "#dc2626",
                         border: `1px solid ${marketOpen ? "rgba(22,163,74,0.3)" : "rgba(220,38,38,0.3)"}` }}>
                <span className={`w-2 h-2 rounded-full ${marketOpen ? "bg-green-500 animate-pulse" : "bg-red-500"}`} />
                {marketOpen ? ne("बजार खुला", "Market Open") : ne("बजार बन्द", "Market Closed")}
              </span>
              {/* Refresh button + countdown */}
              <button onClick={refresh}
                className="flex items-center gap-2 text-xs px-3 py-1.5 rounded-lg transition-all hover:opacity-80"
                style={{ background: "var(--surface)", color: "var(--muted)", border: "1px solid var(--border)" }}>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={2} className="w-3.5 h-3.5">
                  <polyline points="23 4 23 10 17 10" /><polyline points="1 20 1 14 7 14" />
                  <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15" />
                </svg>
                {ne("अपडेट", "Refresh")} ({countdown}s)
              </button>
            </div>
          </div>

          {/* Index Cards */}
          <div className="grid grid-cols-2 md:grid-cols-4 gap-3 mb-8">
            {data.indices.map((idx) => (
              <div key={idx.name} className="rounded-xl p-4 transition-shadow hover:shadow-md"
                style={{ background: "var(--surface)", border: "1px solid var(--border)" }}>
                <p className="text-xs font-semibold mb-1" style={{ color: "var(--muted)" }}>
                  {language === "ne" ? idx.nameNe : idx.name}
                </p>
                <p className="text-xl font-bold tabular-nums" style={{ color: "var(--foreground)" }}>
                  {idx.value.toLocaleString()}
                </p>
                <p className={`text-sm font-semibold mt-1 flex items-center gap-1 ${idx.change >= 0 ? "text-green-600" : "text-red-500"}`}>
                  {idx.change >= 0 ? "▲" : "▼"} {Math.abs(idx.change).toFixed(2)}
                  <span className="text-xs opacity-80">({Math.abs(idx.pct).toFixed(2)}%)</span>
                </p>
              </div>
            ))}
          </div>

          {/* Sector Filter */}
          <div className="flex gap-2 flex-wrap mb-4">
            {SECTORS.map((s) => (
              <button key={s} onClick={() => setSector(s)}
                className="px-3 py-1.5 rounded-full text-sm font-medium transition-all"
                style={{
                  background: sector === s ? "var(--primary)" : "var(--surface)",
                  color: sector === s ? "#fff" : "var(--foreground)",
                  border: "1px solid var(--border)",
                }}>
                {s}
              </button>
            ))}
          </div>

          {/* Stock Table */}
          <div className="rounded-xl overflow-hidden shadow-sm" style={{ background: "var(--surface)", border: "1px solid var(--border)" }}>
            <div className="px-5 py-3 flex items-center justify-between"
              style={{ background: "var(--primary)", color: "#fff" }}>
              <h2 className="font-bold">{ne("शीर्ष कारोबार शेयरहरू", "Top Traded Stocks")}</h2>
              {lastUpdate && (
                <span className="text-xs opacity-80">
                  {ne("अन्तिम अपडेट:", "Last updated:")} {lastUpdate.toLocaleTimeString()}
                </span>
              )}
            </div>
            <div className="overflow-x-auto">
              <table className="w-full text-sm">
                <thead>
                  <tr style={{ borderBottom: "2px solid var(--border)", background: "var(--surface-alt)" }}>
                    {[
                      { label: ne("सिम्बोल","Symbol"), cls: "text-left px-4 py-3" },
                      { label: ne("नाम","Name"), cls: "text-left px-4 py-3 hidden md:table-cell" },
                      { label: ne("LTP","LTP"), cls: "text-right px-4 py-3" },
                      { label: ne("परिवर्तन","Change"), cls: "text-right px-4 py-3" },
                      { label: "%", cls: "text-right px-4 py-3" },
                      { label: ne("उच्च","High"), cls: "text-right px-4 py-3 hidden lg:table-cell" },
                      { label: ne("न्यून","Low"), cls: "text-right px-4 py-3 hidden lg:table-cell" },
                      { label: ne("भोल्युम","Volume"), cls: "text-right px-4 py-3 hidden sm:table-cell" },
                    ].map((h) => (
                      <th key={h.label} className={`${h.cls} font-semibold text-xs uppercase tracking-wide`}
                        style={{ color: "var(--muted)" }}>{h.label}</th>
                    ))}
                  </tr>
                </thead>
                <tbody>
                  {data.stocks.map((stock) => (
                    <tr key={stock.symbol}
                      className="transition-colors cursor-pointer"
                      style={{ borderBottom: "1px solid var(--border)" }}
                      onMouseOver={(e) => (e.currentTarget.style.background = "var(--primary-light)")}
                      onMouseOut={(e)  => (e.currentTarget.style.background = "transparent")}>
                      <td className="px-4 py-3 font-bold tracking-wide" style={{ color: "var(--primary)" }}>
                        {stock.symbol}
                      </td>
                      <td className="px-4 py-3 hidden md:table-cell text-sm" style={{ color: "var(--muted)" }}>
                        {stock.name}
                      </td>
                      <td className="px-4 py-3 text-right font-semibold tabular-nums" style={{ color: "var(--foreground)" }}>
                        {fmt(stock.price)}
                      </td>
                      <td className={`px-4 py-3 text-right font-semibold tabular-nums ${stock.change >= 0 ? "text-green-600" : "text-red-500"}`}>
                        {stock.change >= 0 ? "+" : ""}{stock.change.toFixed(2)}
                      </td>
                      <td className="px-4 py-3 text-right">
                        <span className={`inline-block px-2 py-0.5 rounded text-xs font-semibold ${stock.pct >= 0 ? "text-green-700" : "text-red-600"}`}
                          style={{ background: stock.pct >= 0 ? "rgba(22,163,74,0.1)" : "rgba(220,38,38,0.1)" }}>
                          {stock.pct >= 0 ? "+" : ""}{stock.pct.toFixed(2)}%
                        </span>
                      </td>
                      <td className="px-4 py-3 text-right hidden lg:table-cell text-green-600 tabular-nums text-sm">
                        {fmt(stock.high)}
                      </td>
                      <td className="px-4 py-3 text-right hidden lg:table-cell text-red-500 tabular-nums text-sm">
                        {fmt(stock.low)}
                      </td>
                      <td className="px-4 py-3 text-right hidden sm:table-cell tabular-nums text-sm" style={{ color: "var(--muted)" }}>
                        {stock.volume.toLocaleString()}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>

          <div className="mt-4 rounded-xl p-4 flex items-start gap-3 text-sm"
            style={{ background: isLive ? "rgba(22,163,74,0.08)" : "var(--surface-alt)", color: "var(--muted)", border: `1px solid ${isLive ? "rgba(22,163,74,0.3)" : "var(--border)"}` }}>
            <span className="text-lg shrink-0">{isLive ? "🟢" : "💡"}</span>
            <span>
              {isLive
                ? ne("वास्तविक NEPSE डाटा — nepalstock.com.np बाट।", "Live NEPSE data sourced from nepalstock.com.np.")
                : ne(
                    "यो सिमुलेटेड बजार डाटा हो। NEPSE API उपलब्ध हुँदा स्वचालित रूपमा लाइभ डाटा देखाउनेछ।",
                    "Simulated market data. Will auto-show live NEPSE data when the API is reachable."
                  )}
            </span>
          </div>
        </div>
      </main>
      <Footer />
    </>
  );
}
