"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";

export function AdminHolidayActions() {
  const router = useRouter();
  const [show, setShow] = useState(false);
  const [loading, setLoading] = useState(false);
  const [form, setForm] = useState({
    title: "", title_en: "", bs_year: 2082, bs_month: 1, bs_day: 1,
    ad_date: "", type: "public", is_public: true, description: "", description_en: "",
  });

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setLoading(true);
    try {
      const res = await fetch("/api/v1/admin/holidays", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(form),
      });
      if (res.ok) { router.refresh(); setShow(false); setForm({ ...form, title: "", title_en: "", description: "", description_en: "" }); }
    } finally { setLoading(false); }
  }

  if (!show) return (
    <button onClick={() => setShow(true)}
      className="px-4 py-2 rounded-lg text-sm font-bold text-white"
      style={{ background: "var(--accent)" }}>
      + Add Holiday
    </button>
  );

  const inputStyle = { background: "var(--surface)", color: "var(--foreground)", border: "1px solid var(--border)" };

  return (
    <form onSubmit={handleSubmit} className="card p-4 space-y-3">
      <h2 className="text-sm font-bold">Add New Holiday</h2>
      <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
        <input placeholder="Title (Nepali)" value={form.title} onChange={e => setForm({ ...form, title: e.target.value })}
          className="text-sm px-3 py-2 rounded-lg" style={inputStyle} required />
        <input placeholder="Title (English)" value={form.title_en} onChange={e => setForm({ ...form, title_en: e.target.value })}
          className="text-sm px-3 py-2 rounded-lg" style={inputStyle} />
        <div className="flex gap-2">
          <input type="number" placeholder="BS Year" value={form.bs_year} onChange={e => setForm({ ...form, bs_year: +e.target.value })}
            className="text-sm px-3 py-2 rounded-lg w-24" style={inputStyle} required />
          <input type="number" placeholder="Month" value={form.bs_month} onChange={e => setForm({ ...form, bs_month: +e.target.value })}
            className="text-sm px-3 py-2 rounded-lg w-20" style={inputStyle} required min={1} max={12} />
          <input type="number" placeholder="Day" value={form.bs_day} onChange={e => setForm({ ...form, bs_day: +e.target.value })}
            className="text-sm px-3 py-2 rounded-lg w-20" style={inputStyle} required min={1} max={32} />
        </div>
        <input type="date" placeholder="AD Date" value={form.ad_date} onChange={e => setForm({ ...form, ad_date: e.target.value })}
          className="text-sm px-3 py-2 rounded-lg" style={inputStyle} required />
        <select value={form.type} onChange={e => setForm({ ...form, type: e.target.value })}
          className="text-sm px-3 py-2 rounded-lg" style={inputStyle}>
          <option value="public">Public</option>
          <option value="cultural">Cultural</option>
        </select>
        <label className="flex items-center gap-2 text-sm">
          <input type="checkbox" checked={form.is_public} onChange={e => setForm({ ...form, is_public: e.target.checked })} />
          Public Holiday
        </label>
      </div>
      <div className="flex gap-2">
        <button type="submit" disabled={loading}
          className="px-4 py-2 rounded-lg text-sm font-bold text-white"
          style={{ background: "var(--accent)" }}>
          {loading ? "Saving..." : "Save"}
        </button>
        <button type="button" onClick={() => setShow(false)}
          className="px-4 py-2 rounded-lg text-sm" style={{ color: "var(--muted)" }}>
          Cancel
        </button>
      </div>
    </form>
  );
}
