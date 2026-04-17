import { NextResponse } from "next/server";

export const dynamic = "force-dynamic";
export const revalidate = 60; // cache for 60 seconds

const NEPSE_BASE = "https://nepalstock.com.np/api/nots";

const HEADERS = {
  "Accept": "application/json, text/plain, */*",
  "Accept-Language": "en-US,en;q=0.9",
  "Referer": "https://nepalstock.com.np/",
  "User-Agent":
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36",
};

async function fetchNepse<T>(path: string): Promise<T | null> {
  try {
    const res = await fetch(`${NEPSE_BASE}${path}`, {
      headers: HEADERS,
      next: { revalidate: 60 },
    });
    if (!res.ok) return null;
    const json = await res.json();
    return json as T;
  } catch {
    return null;
  }
}

interface NepseIndexRaw {
  index?: number | string;
  absoluteChange?: number | string;
  percentageChange?: number | string;
  totalTurnover?: number | string;
  totalSharesTraded?: number | string;
  totalTransactions?: number | string;
  marketCapitalization?: number | string;
  floatedMarketCapitalization?: number | string;
  sensitiveIndex?: number | string;
  sensitiveFloatIndex?: number | string;
  floatIndex?: number | string;
  sensitiveIndexChange?: number | string;
  sensitiveFloatIndexChange?: number | string;
  floatIndexChange?: number | string;
}

interface NepseStockRaw {
  symbol?: string;
  securityName?: string;
  lastTradedPrice?: number | string;
  priceChange?: number | string;
  percentageChange?: number | string;
  totalTradeQuantity?: number | string;
  highPrice?: number | string;
  lowPrice?: number | string;
  openPrice?: number | string;
}

export async function GET() {
  // Attempt to fetch real NEPSE data
  const [indexData, topTrades] = await Promise.all([
    fetchNepse<NepseIndexRaw>("/nepse-data/index"),
    fetchNepse<{ content?: NepseStockRaw[] }>("/top-ten-trade/share"),
  ]);

  if (indexData && topTrades) {
    const toNum = (v: number | string | undefined, fallback = 0) =>
      v !== undefined && v !== null ? Number(v) : fallback;

    const indices = [
      {
        name: "NEPSE",
        nameNe: "नेप्से",
        value: toNum(indexData.index),
        change: toNum(indexData.absoluteChange),
        pct: toNum(indexData.percentageChange),
      },
      {
        name: "Sensitive",
        nameNe: "सेन्सेटिभ",
        value: toNum(indexData.sensitiveIndex),
        change: toNum(indexData.sensitiveIndexChange),
        pct: 0,
      },
      {
        name: "Float",
        nameNe: "फ्लोट",
        value: toNum(indexData.floatIndex),
        change: toNum(indexData.floatIndexChange),
        pct: 0,
      },
      {
        name: "Sensitive Float",
        nameNe: "सेन्सेटिभ फ्लोट",
        value: toNum(indexData.sensitiveFloatIndex),
        change: toNum(indexData.sensitiveFloatIndexChange),
        pct: 0,
      },
    ];

    const stocks = (topTrades?.content ?? []).map((s) => ({
      symbol: s.symbol ?? "",
      name: s.securityName ?? s.symbol ?? "",
      price: toNum(s.lastTradedPrice),
      change: toNum(s.priceChange),
      pct: toNum(s.percentageChange),
      volume: toNum(s.totalTradeQuantity),
      high: toNum(s.highPrice),
      low: toNum(s.lowPrice),
    }));

    return NextResponse.json(
      { live: true, indices, stocks, lastUpdated: new Date().toISOString() },
      {
        headers: {
          "Cache-Control": "public, s-maxage=60, stale-while-revalidate=30",
        },
      }
    );
  }

  // Fallback: return simulated data flagged as not live
  const fallback = generateFallback();
  return NextResponse.json(
    { live: false, ...fallback, lastUpdated: new Date().toISOString() },
    {
      headers: {
        "Cache-Control": "public, s-maxage=30, stale-while-revalidate=15",
      },
    }
  );
}

function generateFallback() {
  const base = [
    { symbol: "NABIL",  name: "नबिल बैंक",                 basePrice: 1245, vol: 12450 },
    { symbol: "NLIC",   name: "नेपाल लाइफ इन्स्योरेन्स",    basePrice: 1024, vol: 8920  },
    { symbol: "SCB",    name: "स्ट्यान्डर्ड चार्टर्ड बैंक", basePrice: 876,  vol: 3450  },
    { symbol: "SBI",    name: "नेपाल एसबीआई बैंक",          basePrice: 456,  vol: 15680 },
    { symbol: "HBL",    name: "हिमालयन बैंक",               basePrice: 1478, vol: 9870  },
    { symbol: "GBIME",  name: "ग्लोबल आइएमई बैंक",          basePrice: 342,  vol: 21340 },
    { symbol: "NTC",    name: "नेपाल टेलिकम",               basePrice: 789,  vol: 4560  },
    { symbol: "NIFRA",  name: "नेपाल इन्फ्रास्ट्रक्चर",      basePrice: 567,  vol: 7890  },
    { symbol: "SHIVM",  name: "शिवम् सिमेन्ट्स",            basePrice: 445,  vol: 6780  },
    { symbol: "CHDC",   name: "चिल्ड्रेन डेभलपमेन्ट",       basePrice: 456,  vol: 5670  },
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
      low: +(price * (1 - Math.random() * 0.02)).toFixed(2),
    };
  });

  const nepseBase = 2456.78;
  const nc = +(nepseBase * (Math.random() - 0.5) * 0.03).toFixed(2);

  const indices = [
    { name: "NEPSE",           nameNe: "नेप्से",          value: +(nepseBase + nc).toFixed(2), change: nc, pct: +((nc / nepseBase) * 100).toFixed(2) },
    { name: "Sensitive",       nameNe: "सेन्सेटिभ",        value: +(467.89 + (Math.random() - 0.5) * 12).toFixed(2), change: +((Math.random() - 0.5) * 12).toFixed(2), pct: +((Math.random() - 0.5) * 3).toFixed(2) },
    { name: "Float",           nameNe: "फ्लोट",            value: +(198.34 + (Math.random() - 0.5) * 6).toFixed(2),  change: +((Math.random() - 0.5) * 6).toFixed(2),  pct: +((Math.random() - 0.5) * 3).toFixed(2) },
    { name: "Sensitive Float", nameNe: "सेन्सेटिभ फ्लोट", value: +(134.56 + (Math.random() - 0.5) * 4).toFixed(2), change: +((Math.random() - 0.5) * 4).toFixed(2), pct: +((Math.random() - 0.5) * 3).toFixed(2) },
  ];

  return { stocks, indices };
}
