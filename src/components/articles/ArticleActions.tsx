"use client";

import { useState } from "react";
import { Bookmark, Check, Copy, LoaderCircle, Printer, Share2 } from "lucide-react";
import { useLanguage } from "@/contexts/LanguageContext";

export type ArticleActionsProps = {
  articleId: string;
  title: string;
  url: string;
};

export function ArticleActions({ articleId, title, url }: ArticleActionsProps) {
  const { language, t } = useLanguage();
  const [saved, setSaved] = useState(false);
  const [pending, setPending] = useState(false);
  const [feedback, setFeedback] = useState<string | null>(null);

  const copyLink = async () => {
    try {
      await navigator.clipboard.writeText(url);
      setFeedback(language === "ne" ? "लिंक कपी भयो" : "Link copied");
    } catch {
      setFeedback(language === "ne" ? "लिंक कपी गर्न सकिएन" : "Could not copy link");
    }
  };

  const share = async () => {
    if (navigator.share) {
      try {
        await navigator.share({ title, url });
        return;
      } catch {
        return;
      }
    }
    await copyLink();
  };

  const toggleBookmark = async () => {
    setPending(true);
    setFeedback(null);
    try {
      const response = await fetch(saved ? `/api/v1/bookmarks/${articleId}` : "/api/v1/bookmarks", {
        method: saved ? "DELETE" : "POST",
        headers: { "Content-Type": "application/json" },
        body: saved ? undefined : JSON.stringify({ articleId }),
      });
      if (response.status === 401) {
        window.location.assign(`/auth/login?callbackUrl=${encodeURIComponent(window.location.pathname)}`);
        return;
      }
      if (!response.ok) throw new Error("bookmark request failed");
      setSaved(!saved);
      setFeedback(!saved ? (language === "ne" ? "बुकमार्कमा सुरक्षित भयो" : "Saved to bookmarks") : (language === "ne" ? "बुकमार्कबाट हटाइयो" : "Removed from bookmarks"));
    } catch {
      setFeedback(language === "ne" ? "बुकमार्क गर्न सकिएन" : "Could not update bookmark");
    } finally {
      setPending(false);
    }
  };

  return (
    <div className="flex flex-wrap items-center gap-2" aria-label={language === "ne" ? "समाचारका कार्यहरू" : "Article actions"}>
      <button type="button" onClick={toggleBookmark} disabled={pending} className="btn-secondary inline-flex items-center gap-2" aria-pressed={saved}>
        {pending ? <LoaderCircle className="h-4 w-4 animate-spin" aria-hidden="true" /> : saved ? <Check className="h-4 w-4" aria-hidden="true" /> : <Bookmark className="h-4 w-4" aria-hidden="true" />}
        <span>{saved ? (language === "ne" ? "सुरक्षित" : "Saved") : t("common.bookmarks")}</span>
      </button>
      <button type="button" onClick={share} className="btn-secondary inline-flex items-center gap-2"><Share2 className="h-4 w-4" aria-hidden="true" /><span>{t("common.share")}</span></button>
      <button type="button" onClick={copyLink} className="btn-secondary inline-flex items-center gap-2"><Copy className="h-4 w-4" aria-hidden="true" /><span>{feedback?.includes("cop") || feedback?.includes("कपी") ? feedback : t("common.copyLink")}</span></button>
      <button type="button" onClick={() => window.print()} className="btn-secondary inline-flex items-center gap-2"><Printer className="h-4 w-4" aria-hidden="true" /><span>{t("common.print")}</span></button>
      {feedback && !feedback.includes("cop") && !feedback.includes("कपी") ? <span className="w-full text-xs text-muted" role="status">{feedback}</span> : null}
    </div>
  );
}
