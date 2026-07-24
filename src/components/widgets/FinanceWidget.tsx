"use client";

import { useEffect, useState } from "react";
import Link from "next/link";
import { ArrowLeftRight, ArrowRight, Coins } from "lucide-react";
import { useLanguage, toNepaliDigits } from "@/contexts/LanguageContext";

interface ExchangeRate {
  code: string;
  name: string;
  name_ne: string;
  buy: number;
  sell: number;
  unit: number;
}

interface GoldSilverData {
  gold: { tola_24k: number; tola_22k: number; gram_24k: number; international_oz_usd: number };
  silver: { tola: number; gram: number; international_oz_usd: number };
}

const MAIN_CURRENCIES = ["USD", "EUR", "GBP", "INR", "AUD"];

function formatNepali(value: number, language: "ne" | "en"): string {
  if (language === "ne") return toNepaliDigits(value);
  return value.toLocaleString("en-NP");
}

export function FinanceWidget() {
  const { language } = useLanguage();
  const isNe = language === "ne";
  const [rates, setRates] = useState<ExchangeRate[]>([]);
  const [goldSilver, setGoldSilver] = useState<GoldSilverData | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    Promise.all([
      fetch("/api/v1/finance/exchange-rates").then((r) => r.json()),
      fetch("/api/v1/finance/gold-silver").then((r) => r.json()),
    ])
      .then(([rateRes, gsRes]) => {
        if (rateRes.success) {
          setRates(
            rateRes.data.filter((r: ExchangeRate) =>
              MAIN_CURRENCIES.includes(r.code),
            ),
          );
        }
        if (gsRes.success) setGoldSilver(gsRes.data);
      })
      .catch(() => {})
      .finally(() => setLoading(false));
  }, []);

  if (loading) {
    return (
      <section
        className="rounded-xl border border-border bg-surface p-4 shadow-sm"
        aria-label={isNe ? "वित्तीय डाटा" : "Financial data"}
      >
        <div className="mb-3 h-5 w-32 rounded skeleton" />
        <div className="space-y-2">
          {[1, 2, 3].map((i) => (
            <div key={i} className="h-4 rounded skeleton" />
          ))}
        </div>
      </section>
    );
  }

  return (
    <section
      className="rounded-xl border border-border bg-surface shadow-sm overflow-hidden"
      aria-label={isNe ? "वित्तीय डाटा" : "Financial data"}
    >
      {/* Header */}
      <header className="flex items-center justify-between gap-3 border-b border-border bg-surface-alt/40 px-4 py-3">
        <div className="flex items-center gap-2.5">
          <span
            className="grid h-7 w-7 place-items-center rounded-md bg-accent/15 text-accent"
            aria-hidden="true"
          >
            <ArrowLeftRight className="h-3.5 w-3.5" strokeWidth={2.5} />
          </span>
          <h3
            className="text-[15px] font-bold leading-none text-foreground"
            style={{ fontFamily: "var(--font-nepali-serif)" }}
          >
            {isNe ? "विनिमय दर" : "Exchange Rates"}
          </h3>
        </div>
        <Link
          href="/finance"
          className="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-[0.16em] text-muted-foreground transition-colors hover:text-accent"
        >
          {isNe ? "सबै" : "All"}
          <ArrowRight className="h-3.5 w-3.5" aria-hidden="true" />
        </Link>
      </header>

      {/* Currencies */}
      <ul className="divide-y divide-border">
        {rates.map((rate) => {
          const label = isNe ? rate.name_ne : rate.name;
          const unitPrefix = rate.unit > 1 ? `${rate.unit} × ` : "";
          return (
            <li key={rate.code}>
              <div className="flex items-center justify-between gap-3 px-4 py-2.5">
                <div className="flex min-w-0 items-center gap-3">
                  <span className="grid h-7 min-w-[2.5rem] place-items-center rounded-md border border-border bg-surface-alt px-2 text-[11px] font-black tracking-[0.04em] text-foreground">
                    {rate.code}
                  </span>
                  <span className="truncate text-[13px] font-medium text-muted-foreground">
                    {unitPrefix}
                    {label}
                  </span>
                </div>
                <div className="text-right">
                  <div className="text-[15px] font-bold tabular-nums text-foreground">
                    {isNe ? "रु. " : "Rs. "}
                    {formatNepali(rate.buy, language)}
                  </div>
                  <div className="text-[10px] font-semibold uppercase tracking-wider text-muted-foreground">
                    {isNe ? "बिक्री" : "Buy"}
                  </div>
                </div>
              </div>
            </li>
          );
        })}
      </ul>

      {/* Gold / Silver */}
      {goldSilver && (
        <>
          <header className="flex items-center justify-between gap-3 border-y border-border bg-surface-alt/40 px-4 py-3">
            <div className="flex items-center gap-2.5">
              <span
                className="grid h-7 w-7 place-items-center rounded-md bg-amber-100 text-amber-700"
                aria-hidden="true"
              >
                <Coins className="h-3.5 w-3.5" strokeWidth={2.5} />
              </span>
              <h3
                className="text-[15px] font-bold leading-none text-foreground"
                style={{ fontFamily: "var(--font-nepali-serif)" }}
              >
                {isNe ? "सुन-चाँदी दर" : "Gold & Silver"}
              </h3>
            </div>
            <Link
              href="/finance#gold"
              className="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-[0.16em] text-muted-foreground transition-colors hover:text-accent"
            >
              {isNe ? "सबै" : "All"}
              <ArrowRight className="h-3.5 w-3.5" aria-hidden="true" />
            </Link>
          </header>
          <ul className="divide-y divide-border">
            <li>
              <div className="flex items-center justify-between gap-3 px-4 py-2.5">
                <div>
                  <div className="text-[13px] font-semibold text-foreground">
                    {isNe ? "सुन (२४क)" : "Gold (24K)"}
                  </div>
                  <div className="text-[10px] font-semibold uppercase tracking-wider text-muted-foreground">
                    / {isNe ? "तोला" : "Tola"}
                  </div>
                </div>
                <div className="text-[15px] font-black tabular-nums text-amber-700">
                  {isNe ? "रु. " : "Rs. "}
                  {formatNepali(goldSilver.gold.tola_24k, language)}
                </div>
              </div>
            </li>
            <li>
              <div className="flex items-center justify-between gap-3 px-4 py-2.5">
                <div>
                  <div className="text-[13px] font-semibold text-foreground">
                    {isNe ? "सुन (२२क)" : "Gold (22K)"}
                  </div>
                  <div className="text-[10px] font-semibold uppercase tracking-wider text-muted-foreground">
                    / {isNe ? "तोला" : "Tola"}
                  </div>
                </div>
                <div className="text-[15px] font-black tabular-nums text-amber-700">
                  {isNe ? "रु. " : "Rs. "}
                  {formatNepali(goldSilver.gold.tola_22k, language)}
                </div>
              </div>
            </li>
            <li>
              <div className="flex items-center justify-between gap-3 px-4 py-2.5">
                <div>
                  <div className="text-[13px] font-semibold text-foreground">
                    {isNe ? "चाँदी" : "Silver"}
                  </div>
                  <div className="text-[10px] font-semibold uppercase tracking-wider text-muted-foreground">
                    / {isNe ? "तोला" : "Tola"}
                  </div>
                </div>
                <div className="text-[15px] font-black tabular-nums text-slate-500">
                  {isNe ? "रु. " : "Rs. "}
                  {formatNepali(goldSilver.silver.tola, language)}
                </div>
              </div>
            </li>
          </ul>
        </>
      )}

      <Link
        href="/finance"
        className="flex items-center justify-center gap-1 border-t border-border bg-surface-alt/60 px-4 py-2.5 text-[12px] font-bold uppercase tracking-wider text-foreground transition-colors hover:bg-accent hover:text-white"
      >
        {isNe ? "पूर्ण वित्तीय डाटा हेर्नुहोस्" : "View Full Financial Data"}
        <ArrowRight className="h-3.5 w-3.5" aria-hidden="true" />
      </Link>
    </section>
  );
}
