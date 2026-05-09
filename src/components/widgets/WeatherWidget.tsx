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

export function WeatherWidget() {
  const { language } = useLanguage();
  const [weather, setWeather] = useState<WeatherData | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
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
      .catch(() => setWeather(null))
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
