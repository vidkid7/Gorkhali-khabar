"use client";

import { ArrowDown, ArrowUp, Pencil, Plus, ToggleLeft, ToggleRight, Trash2 } from "lucide-react";
import { useEffect, useMemo, useState } from "react";

interface QuickLink {
  id: string;
  slug: string;
  href: string;
  title_ne: string;
  title_en: string;
  description_ne: string;
  description_en: string;
  icon_key: string;
  accent_color: string;
  sort_order: number;
  is_active: boolean;
}

const ICON_OPTIONS = [
  "CalendarDays",
  "Sparkles",
  "ChartNoAxesCombined",
  "Landmark",
  "Coins",
  "CloudSun",
  "Calculator",
  "Heart",
  "Image",
  "Mail",
  "MapPin",
  "Newspaper",
];

const ACCENT_PRESETS = [
  "#c62828",
  "#6a1b9a",
  "#07579b",
  "#2e7d32",
  "#b45309",
  "#00838f",
  "#283593",
  "#bf360c",
  "#4e342e",
];

export default function AdminQuickLinksPage() {
  const [links, setLinks] = useState<QuickLink[]>([]);
  const [message, setMessage] = useState("");
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(true);
  const [editingId, setEditingId] = useState<string | null>(null);

  const empty = {
    slug: "",
    href: "",
    title_ne: "",
    title_en: "",
    description_ne: "",
    description_en: "",
    icon_key: "CalendarDays",
    accent_color: "#c62828",
    sort_order: 0,
    is_active: true,
  };

  const [form, setForm] = useState<Omit<QuickLink, "id">>(empty);

  async function load() {
    setLoading(true);
    try {
      const res = await fetch("/api/v1/admin/quick-links");
      const data = await res.json();
      if (res.ok && data.success) {
        setLinks(data.data);
      } else {
        setError(data.error || "लिंकहरू प्राप्त गर्न सकिएन");
      }
    } catch {
      setError("लिंकहरू प्राप्त गर्दा त्रुटि भयो");
    } finally {
      setLoading(false);
    }
  }

  useEffect(() => {
    load();
  }, []);

  function startEdit(link: QuickLink) {
    setEditingId(link.id);
    setForm({
      slug: link.slug,
      href: link.href,
      title_ne: link.title_ne,
      title_en: link.title_en,
      description_ne: link.description_ne,
      description_en: link.description_en,
      icon_key: link.icon_key,
      accent_color: link.accent_color,
      sort_order: link.sort_order,
      is_active: link.is_active,
    });
  }

  function cancelEdit() {
    setEditingId(null);
    setForm(empty);
    setMessage("");
    setError("");
  }

  async function submitForm(event: React.FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setMessage("");
    setError("");

    const payload = {
      ...form,
      sort_order: Number(form.sort_order) || 0,
      is_active: Boolean(form.is_active),
    };

    try {
      const url = editingId ? `/api/v1/admin/quick-links/${editingId}` : "/api/v1/admin/quick-links";
      const method = editingId ? "PATCH" : "POST";
      const res = await fetch(url, {
        method,
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      });
      const data = await res.json();
      if (!res.ok || !data.success) {
        setError(data.error || "लिंक बचत गर्न सकिएन");
        return;
      }
      setMessage(editingId ? "लिंक अपडेट भयो।" : "नयाँ लिंक थपियो।");
      cancelEdit();
      await load();
    } catch {
      setError("लिंक बचत गर्दा त्रुटि भयो");
    }
  }

  async function toggleActive(link: QuickLink) {
    try {
      const res = await fetch(`/api/v1/admin/quick-links/${link.id}`, {
        method: "PATCH",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ is_active: !link.is_active }),
      });
      const data = await res.json();
      if (res.ok && data.success) {
        await load();
      } else {
        setError(data.error || "अवस्था परिवर्तन गर्न सकिएन");
      }
    } catch {
      setError("अवस्था परिवर्तन गर्दा त्रुटि भयो");
    }
  }

  async function removeLink(link: QuickLink) {
    if (!confirm(`"${link.title_ne}" लिंक हटाउनुहोस्?`)) return;
    try {
      const res = await fetch(`/api/v1/admin/quick-links/${link.id}`, { method: "DELETE" });
      const data = await res.json();
      if (res.ok && data.success) {
        await load();
      } else {
        setError(data.error || "लिंक हटाउन सकिएन");
      }
    } catch {
      setError("लिंक हटाउँदा त्रुटि भयो");
    }
  }

  async function moveOrder(link: QuickLink, direction: -1 | 1) {
    const sorted = [...links].sort((a, b) => a.sort_order - b.sort_order);
    const idx = sorted.findIndex((l) => l.id === link.id);
    const next = sorted[idx + direction];
    if (!next) return;
    const a = link.sort_order;
    const b = next.sort_order;
    try {
      await Promise.all([
        fetch(`/api/v1/admin/quick-links/${link.id}`, {
          method: "PATCH",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ sort_order: b }),
        }),
        fetch(`/api/v1/admin/quick-links/${next.id}`, {
          method: "PATCH",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ sort_order: a }),
        }),
      ]);
      await load();
    } catch {
      setError("क्रम परिवर्तन गर्दा त्रुटि भयो");
    }
  }

  const sortedLinks = useMemo(() => [...links].sort((a, b) => a.sort_order - b.sort_order), [links]);

  const inputCls =
    "w-full rounded-md border border-border bg-surface px-3 py-2 text-sm text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20";

  return (
    <div className="space-y-6">
      <header className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold tracking-tight" style={{ fontFamily: "var(--font-nepali-serif)" }}>
            &ldquo;आजका औजार&rdquo; द्रुत लिंक
          </h1>
          <p className="text-sm text-muted-foreground">
            गृहपृष्ठको &ldquo;Today&rsquo;s Tools&rdquo; खण्डका लिंकहरू यहाँबाट व्यवस्थापन गर्नुहोस्। सबै सार्वजनिक API बाट पढिन्छ।
          </p>
        </div>
        <button
          type="button"
          onClick={() => {
            cancelEdit();
            setEditingId(null);
            setForm({ ...empty, sort_order: (sortedLinks.at(-1)?.sort_order ?? 0) + 10 });
          }}
          className="inline-flex items-center gap-1.5 rounded-md bg-accent px-3 py-2 text-sm font-semibold text-white transition-colors hover:bg-accent-hover"
        >
          <Plus className="h-4 w-4" /> नयाँ लिंक
        </button>
      </header>

      {message && (
        <p className="rounded-md border border-success/30 bg-success/10 px-3 py-2 text-sm font-medium text-success">
          {message}
        </p>
      )}
      {error && (
        <p className="rounded-md border border-error/30 bg-error/10 px-3 py-2 text-sm font-medium text-error">
          {error}
        </p>
      )}

      <form onSubmit={submitForm} className="grid gap-3 rounded-xl border border-border bg-surface-alt/30 p-4 sm:grid-cols-2">
        <label className="grid gap-1 text-xs font-semibold uppercase tracking-wider text-muted-foreground">
          Slug
          <input
            value={form.slug}
            onChange={(e) => setForm({ ...form, slug: e.target.value })}
            placeholder="patro"
            className={inputCls}
            required
            pattern="[a-z0-9-]+"
          />
        </label>
        <label className="grid gap-1 text-xs font-semibold uppercase tracking-wider text-muted-foreground">
          Href
          <input
            value={form.href}
            onChange={(e) => setForm({ ...form, href: e.target.value })}
            placeholder="/patro"
            className={inputCls}
            required
          />
        </label>
        <label className="grid gap-1 text-xs font-semibold uppercase tracking-wider text-muted-foreground">
          शीर्षक (नेपाली)
          <input
            value={form.title_ne}
            onChange={(e) => setForm({ ...form, title_ne: e.target.value })}
            placeholder="पात्रो"
            className={inputCls}
            required
          />
        </label>
        <label className="grid gap-1 text-xs font-semibold uppercase tracking-wider text-muted-foreground">
          Title (English)
          <input
            value={form.title_en}
            onChange={(e) => setForm({ ...form, title_en: e.target.value })}
            placeholder="Patro"
            className={inputCls}
            required
          />
        </label>
        <label className="grid gap-1 text-xs font-semibold uppercase tracking-wider text-muted-foreground">
          विवरण (नेपाली)
          <input
            value={form.description_ne}
            onChange={(e) => setForm({ ...form, description_ne: e.target.value })}
            placeholder="नेपाली पात्रो र मिति"
            className={inputCls}
            required
          />
        </label>
        <label className="grid gap-1 text-xs font-semibold uppercase tracking-wider text-muted-foreground">
          Description (English)
          <input
            value={form.description_en}
            onChange={(e) => setForm({ ...form, description_en: e.target.value })}
            placeholder="Nepali calendar & dates"
            className={inputCls}
            required
          />
        </label>
        <label className="grid gap-1 text-xs font-semibold uppercase tracking-wider text-muted-foreground">
          Icon
          <select
            value={form.icon_key}
            onChange={(e) => setForm({ ...form, icon_key: e.target.value })}
            className={inputCls}
          >
            {ICON_OPTIONS.map((opt) => (
              <option key={opt} value={opt}>
                {opt}
              </option>
            ))}
          </select>
        </label>
        <label className="grid gap-1 text-xs font-semibold uppercase tracking-wider text-muted-foreground">
          Accent color
          <div className="flex items-center gap-2">
            <input
              value={form.accent_color}
              onChange={(e) => setForm({ ...form, accent_color: e.target.value })}
              className={inputCls}
              required
              pattern="#[0-9a-fA-F]{6}"
            />
            <span
              className="h-7 w-7 shrink-0 rounded-md border border-border"
              style={{ background: form.accent_color }}
              aria-hidden="true"
            />
            <div className="flex gap-1">
              {ACCENT_PRESETS.map((c) => (
                <button
                  type="button"
                  key={c}
                  onClick={() => setForm({ ...form, accent_color: c })}
                  className="h-5 w-5 rounded-full border border-border"
                  style={{ background: c }}
                  aria-label={`Set ${c}`}
                />
              ))}
            </div>
          </div>
        </label>
        <label className="grid gap-1 text-xs font-semibold uppercase tracking-wider text-muted-foreground">
          Sort order
          <input
            type="number"
            value={form.sort_order}
            onChange={(e) => setForm({ ...form, sort_order: parseInt(e.target.value || "0", 10) })}
            className={inputCls}
          />
        </label>
        <label className="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-muted-foreground">
          <input
            type="checkbox"
            checked={form.is_active}
            onChange={(e) => setForm({ ...form, is_active: e.target.checked })}
            className="h-4 w-4"
          />
          Active (show on homepage)
        </label>

        <div className="sm:col-span-2 flex flex-wrap items-center gap-2 pt-1">
          <button
            type="submit"
            className="inline-flex items-center gap-1.5 rounded-md bg-primary px-4 py-2 text-sm font-semibold text-primary-foreground transition-colors hover:bg-primary/90"
          >
            <Pencil className="h-4 w-4" /> {editingId ? "अपडेट गर्नुहोस्" : "नयाँ सुरक्षित गर्नुहोस्"}
          </button>
          {editingId && (
            <button
              type="button"
              onClick={cancelEdit}
              className="rounded-md border border-border bg-surface px-4 py-2 text-sm font-semibold text-foreground transition-colors hover:bg-surface-alt"
            >
              रद्द गर्नुहोस्
            </button>
          )}
        </div>
      </form>

      {loading ? (
        <p className="text-sm text-muted-foreground">लोड हुँदैछ…</p>
      ) : sortedLinks.length === 0 ? (
        <p className="rounded-md border border-dashed border-border bg-surface p-6 text-center text-sm text-muted-foreground">
          कुनै लिंक छैन। माथिको फारमबाट नयाँ थप्नुहोस्।
        </p>
      ) : (
        <div className="grid gap-3">
          {sortedLinks.map((link, idx) => (
            <article
              key={link.id}
              className="flex items-start gap-3 rounded-lg border border-border bg-surface p-4 shadow-sm"
            >
              <span
                className="grid h-10 w-10 shrink-0 place-items-center rounded-md font-bold"
                style={{ background: `${link.accent_color}1a`, color: link.accent_color }}
                aria-hidden="true"
              >
                #{link.sort_order}
              </span>
              <div className="min-w-0 flex-1">
                <div className="mb-1 flex flex-wrap items-center gap-2">
                  <span className="text-[10px] font-bold uppercase tracking-wider text-muted-foreground">
                    /{link.slug}
                  </span>
                  {!link.is_active && (
                    <span className="rounded-full bg-surface-alt px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-muted-foreground">
                      निष्क्रिय
                    </span>
                  )}
                  <span className="text-[10px] font-bold uppercase tracking-wider text-muted-foreground">{link.icon_key}</span>
                </div>
                <h3 className="text-base font-bold leading-tight" style={{ fontFamily: "var(--font-nepali-serif)" }}>
                  {link.title_ne} <span className="font-normal text-muted-foreground">/ {link.title_en}</span>
                </h3>
                <p className="mt-1 text-xs text-muted-foreground">{link.description_ne} / {link.description_en}</p>
                <p className="mt-1 truncate text-xs font-mono text-muted-foreground">
                  {link.href}
                </p>
              </div>
              <div className="flex shrink-0 items-center gap-1">
                <button
                  type="button"
                  onClick={() => moveOrder(link, -1)}
                  disabled={idx === 0}
                  className="grid h-8 w-8 place-items-center rounded-md border border-border bg-surface text-foreground transition-colors hover:bg-surface-alt disabled:opacity-40"
                  aria-label="Move up"
                >
                  <ArrowUp className="h-3.5 w-3.5" />
                </button>
                <button
                  type="button"
                  onClick={() => moveOrder(link, 1)}
                  disabled={idx === sortedLinks.length - 1}
                  className="grid h-8 w-8 place-items-center rounded-md border border-border bg-surface text-foreground transition-colors hover:bg-surface-alt disabled:opacity-40"
                  aria-label="Move down"
                >
                  <ArrowDown className="h-3.5 w-3.5" />
                </button>
                <button
                  type="button"
                  onClick={() => toggleActive(link)}
                  className="grid h-8 w-8 place-items-center rounded-md border border-border bg-surface text-foreground transition-colors hover:bg-surface-alt"
                  aria-label={link.is_active ? "Disable" : "Enable"}
                >
                  {link.is_active ? <ToggleRight className="h-3.5 w-3.5 text-success" /> : <ToggleLeft className="h-3.5 w-3.5" />}
                </button>
                <button
                  type="button"
                  onClick={() => startEdit(link)}
                  className="grid h-8 w-8 place-items-center rounded-md border border-border bg-surface text-foreground transition-colors hover:bg-surface-alt"
                  aria-label="Edit"
                >
                  <Pencil className="h-3.5 w-3.5" />
                </button>
                <button
                  type="button"
                  onClick={() => removeLink(link)}
                  className="grid h-8 w-8 place-items-center rounded-md border border-border bg-surface text-error transition-colors hover:bg-error/10"
                  aria-label="Delete"
                >
                  <Trash2 className="h-3.5 w-3.5" />
                </button>
              </div>
            </article>
          ))}
        </div>
      )}
    </div>
  );
}
