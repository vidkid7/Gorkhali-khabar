"use client";

import Link from "next/link";
import { FormEvent, useState } from "react";
import { useSearchParams } from "next/navigation";
import { BrandWordmark } from "@/components/layout/BrandWordmark";
import { useLanguage } from "@/contexts/LanguageContext";

export default function ResetPasswordPage() {
  const { language, t } = useLanguage();
  const searchParams = useSearchParams();
  const token = searchParams.get("token") || "";
  const [password, setPassword] = useState("");
  const [confirm, setConfirm] = useState("");
  const [message, setMessage] = useState<string | null>(null);
  const [error, setError] = useState<string | null>(token ? null : (language === "ne" ? "रिसेट टोकन आवश्यक छ" : "A reset token is required."));
  const [loading, setLoading] = useState(false);

  async function handleSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    if (password !== confirm) { setError(t("auth.confirmPassword")); return; }
    setLoading(true); setError(null);
    try {
      const response = await fetch("/api/v1/auth/reset-password", { method: "POST", headers: { "Content-Type": "application/json" }, body: JSON.stringify({ token, password }) });
      const data = await response.json();
      if (!response.ok) setError(data.error || t("common.error"));
      else setMessage(data.message || (language === "ne" ? "पासवर्ड रिसेट भयो" : "Password reset successfully."));
    } catch { setError(t("common.error")); } finally { setLoading(false); }
  }

  return <main className="flex min-h-screen items-center justify-center bg-background px-4 py-12"><section className="w-full max-w-md"><div className="mb-8 flex justify-center"><BrandWordmark compact priority /></div><div className="utility-panel p-6 sm:p-8"><h1 className="text-2xl font-bold" style={{ fontFamily: "var(--font-nepali-serif)" }}>{t("auth.resetPassword")}</h1>{message ? <div className="mt-5 space-y-4"><p className="border border-success/30 bg-success-light p-3 text-sm text-success" role="status">{message}</p><Link href="/auth/login" className="btn-primary block text-center">{t("common.login")}</Link></div> : <form onSubmit={handleSubmit} className="mt-6 space-y-4"><div><label htmlFor="new-password" className="block text-sm font-medium">{t("auth.newPassword")}</label><input id="new-password" type="password" required minLength={8} value={password} onChange={(event) => setPassword(event.target.value)} className="input mt-1 w-full" autoComplete="new-password" /></div><div><label htmlFor="confirm-password" className="block text-sm font-medium">{t("auth.confirmPassword")}</label><input id="confirm-password" type="password" required minLength={8} value={confirm} onChange={(event) => setConfirm(event.target.value)} className="input mt-1 w-full" autoComplete="new-password" /></div>{error && <p className="border border-error/30 bg-error-light p-3 text-sm text-error" role="alert">{error}</p>}<button type="submit" disabled={loading || !token} className="btn-primary w-full">{loading ? t("common.loading") : t("common.resetPassword")}</button></form>}</div></section></main>;
}
