"use client";

import Link from "next/link";
import { useEffect, useState } from "react";
import { useLanguage, toNepaliDigits, formatNepaliDate } from "@/contexts/LanguageContext";

interface ExchangeRate {
  code: string;
  buy: number;
}

interface GoldSilverData {
  gold?: { tola_24k?: number };
}

function formatMoney(value?: number, language: "ne" | "en" = "ne") {
  if (typeof value !== "number" || Number.isNaN(value)) return language === "ne" ? "..." : "...";
  const formatted = Math.round(value).toLocaleString("en-NP");
  return language === "ne" ? toNepaliDigits(formatted) : formatted;
}

export function TodayStrip() {
  const { language } = useLanguage();
  const [date] = useState(() => new Date());
  const [usd, setUsd] = useState<number>();
  const [gold, setGold] = useState<number>();

  useEffect(() => {
    Promise.all([
      fetch("/api/v1/finance/exchange-rates").then((r) => r.json()),
      fetch("/api/v1/finance/gold-silver").then((r) => r.json()),
    ])
      .then(([rateRes, goldRes]) => {
        const rates: ExchangeRate[] = Array.isArray(rateRes.data) ? rateRes.data : [];
        setUsd(rates.find((rate) => rate.code === "USD")?.buy);
        const goldData = goldRes.data as GoldSilverData | undefined;
        setGold(goldData?.gold?.tola_24k);
      })
      .catch(() => {
        // Keep the strip useful even if live market data is temporarily unavailable.
      });
  }, []);

  const dateText = language === "ne"
    ? formatNepaliDate(date)
    : date.toLocaleDateString("en-US", { weekday: "short", month: "short", day: "numeric" });

  const labels = {
    today: language === "ne" ? "आज" : "Today",
    kathmandu: language === "ne" ? "काठमाडौं" : "Kathmandu",
    usd: language === "ne" ? "डलर" : "USD",
    gold: language === "ne" ? "सुन" : "Gold",
    brief: language === "ne" ? "५ मिनेट ब्रिफ" : "5 min brief",
  };

  return (
    <section className="mx-auto w-full max-w-7xl px-3 sm:px-4 pt-3">
      <div className="grid grid-cols-2 gap-2 rounded-2xl border border-border bg-surface/95 p-2 shadow-sm sm:grid-cols-5">
        <div className="col-span-2 flex min-w-0 items-center gap-3 rounded-xl bg-gradient-to-r from-blue-700 to-blue-600 px-3 py-2 text-white sm:col-span-1">
          <span className="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-white/15 text-sm font-bold">
            {labels.today}
          </span>
          <div className="min-w-0">
            <p className="truncate text-sm font-bold">{dateText}</p>
            <p className="truncate text-[11px] text-white/75">{labels.kathmandu}</p>
          </div>
        </div>

        <Link href="/finance" className="rounded-xl bg-surface-alt px-3 py-2 transition-colors hover:bg-border">
          <p className="text-[11px] font-semibold text-muted">{labels.usd}</p>
          <p className="text-sm font-extrabold text-foreground">रु. {formatMoney(usd, language)}</p>
        </Link>

        <Link href="/finance#gold" className="rounded-xl bg-surface-alt px-3 py-2 transition-colors hover:bg-border">
          <p className="text-[11px] font-semibold text-muted">{labels.gold}</p>
          <p className="text-sm font-extrabold text-foreground">रु. {formatMoney(gold, language)}</p>
        </Link>

        <Link href="/share-market" className="rounded-xl bg-surface-alt px-3 py-2 transition-colors hover:bg-border">
          <p className="text-[11px] font-semibold text-muted">NEPSE</p>
          <p className="text-sm font-extrabold text-green-600">+0.8%</p>
        </Link>

        <Link href="/#daily-brief" className="rounded-xl bg-red-600 px-3 py-2 text-white transition-colors hover:bg-red-700">
          <p className="text-[11px] font-semibold text-white/75">{language === "ne" ? "छिटो पढ्नुहोस्" : "Read fast"}</p>
          <p className="truncate text-sm font-extrabold">{labels.brief}</p>
        </Link>
      </div>
    </section>
  );
}
