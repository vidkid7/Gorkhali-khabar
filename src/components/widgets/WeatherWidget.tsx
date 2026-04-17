"use client";

import { useState, useEffect } from "react";
import { useLanguage } from "@/contexts/LanguageContext";

interface WeatherData {
  temp: number;
  feels_like: number;
  desc: string;
  desc_ne: string;
  icon: string;
  humidity: number;
  wind: number;
  city: string;
}

// Fallback simulated data for Kathmandu
function getFallback(): WeatherData {
  const conditions = [
    { desc: "Partly Cloudy", desc_ne: "आंशिक बादल", icon: "⛅" },
    { desc: "Sunny", desc_ne: "घाम लागेको", icon: "☀️" },
    { desc: "Overcast", desc_ne: "मेघाच्छादित", icon: "☁️" },
    { desc: "Light Rain", desc_ne: "हलुका वर्षा", icon: "🌦️" },
    { desc: "Foggy", desc_ne: "कुहिरो", icon: "🌫️" },
  ];
  const c = conditions[Math.floor(Math.random() * conditions.length)];
  return {
    temp: Math.round(18 + Math.random() * 12),
    feels_like: Math.round(15 + Math.random() * 10),
    desc: c.desc, desc_ne: c.desc_ne, icon: c.icon,
    humidity: Math.round(55 + Math.random() * 30),
    wind: Math.round(5 + Math.random() * 15),
    city: "Kathmandu",
  };
}

export function WeatherWidget() {
  const { language } = useLanguage();
  const [weather, setWeather] = useState<WeatherData | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    // Try wttr.in API (no key required)
    fetch("https://wttr.in/Kathmandu?format=j1")
      .then((r) => r.json())
      .then((data) => {
        const cc = data.current_condition[0];
        setWeather({
          temp: parseInt(cc.temp_C),
          feels_like: parseInt(cc.FeelsLikeC),
          desc: cc.weatherDesc[0].value,
          desc_ne: cc.weatherDesc[0].value,
          icon: "🌤️",
          humidity: parseInt(cc.humidity),
          wind: parseInt(cc.windspeedKmph),
          city: "Kathmandu",
        });
      })
      .catch(() => setWeather(getFallback()))
      .finally(() => setLoading(false));
  }, []);

  if (loading) {
    return (
      <div className="weather-chip animate-pulse">
        <span>🌡️</span>
        <span className="w-12 h-3 rounded" style={{ background: "var(--border)" }} />
      </div>
    );
  }

  if (!weather) return null;

  return (
    <div className="weather-chip cursor-default" title={language === "ne" ? weather.desc_ne : weather.desc}>
      <span>{weather.icon}</span>
      <span className="font-semibold">{weather.temp}°C</span>
      <span className="hidden sm:inline text-xs" style={{ color: "var(--muted)" }}>
        {language === "ne" ? "काठमाडौं" : "KTM"}
      </span>
    </div>
  );
}
