"use client";

import Link from "next/link";
import { FormEvent, useState } from "react";
import { BrandWordmark } from "@/components/layout/BrandWordmark";
import { useLanguage } from "@/contexts/LanguageContext";

export default function ForgotPasswordPage() {
  const { language, t } = useLanguage();
  const [email, setEmail] = useState("");
  const [message, setMessage] = useState<string | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [loading, setLoading] = useState(false);

  async function handleSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setLoading(true); setError(null); setMessage(null);
    try {
      const response = await fetch("/api/v1/auth/forgot-password", { method: "POST", headers: { "Content-Type": "application/json" }, body: JSON.stringify({ email }) });
      const data = await response.json();
      if (!response.ok) setError(data.error || t("common.error"));
      else setMessage(data.message || (language === "ne" ? "यदि इमेल दर्ता छ भने, लिंक पठाइनेछ" : "If the email is registered, a reset link will be sent."));
    } catch { setError(t("common.error")); } finally { setLoading(false); }
  }

  return <main className="flex min-h-screen items-center justify-center bg-background px-4 py-12"><section className="w-full max-w-md"><div className="mb-8 flex justify-center"><BrandWordmark compact priority /></div><div className="utility-panel p-6 sm:p-8"><h1 className="text-2xl font-bold" style={{ fontFamily: "var(--font-nepali-serif)" }}>{t("auth.forgotPassword")}</h1><p className="mt-2 text-sm text-muted">{language === "ne" ? "आफ्नो इमेल दिनुहोस् र हामी रिसेट लिंक पठाउँछौं।" : "Enter your email and we will send a reset link."}</p>{message && <p className="mt-4 border border-success/30 bg-success-light p-3 text-sm text-success" role="status">{message}</p>}{error && <p className="mt-4 border border-error/30 bg-error-light p-3 text-sm text-error" role="alert">{error}</p>}<form onSubmit={handleSubmit} className="mt-6 space-y-4"><label htmlFor="forgot-email" className="block text-sm font-medium">{t("auth.email")}</label><input id="forgot-email" type="email" required value={email} onChange={(event) => setEmail(event.target.value)} className="input w-full" autoComplete="email" /><button type="submit" disabled={loading} className="btn-primary w-full">{loading ? t("common.loading") : t("common.submit")}</button></form><Link href="/auth/login" className="mt-6 block text-center text-sm text-primary hover:underline">{t("common.login")}</Link></div></section></main>;
}
