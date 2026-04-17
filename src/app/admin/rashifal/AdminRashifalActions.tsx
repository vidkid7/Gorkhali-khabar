"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";

export function AdminRashifalActions({ signs }: { signs: string[] }) {
  const router = useRouter();
  const [show, setShow] = useState(false);
  const [loading, setLoading] = useState(false);
  const [form, setForm] = useState({
    sign: "mesh", sign_ne: "मेष", ad_date: "", bs_year: 2082, bs_month: 1, bs_day: 1, prediction: "", prediction_en: "", rating: 3,
  });

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setLoading(true);
    try {
      const res = await fetch("/api/v1/admin/rashifal", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(form),
      });
      if (res.ok) { router.refresh(); setShow(false); }
    } finally { setLoading(false); }
  }

  if (!show) return (
    <button onClick={() => setShow(true)}
      className="px-4 py-2 rounded-lg text-sm font-bold text-white"
      style={{ background: "var(--accent)" }}>
      + Add Rashifal
    </button>
  );

  const inputStyle = { background: "var(--surface)", color: "var(--foreground)", border: "1px solid var(--border)" };

  return (
    <form onSubmit={handleSubmit} className="card p-4 space-y-3">
      <h2 className="text-sm font-bold">Add Rashifal Entry</h2>
      <div className="grid grid-cols-1 md:grid-cols-3 gap-3">
        <select value={form.sign} onChange={e => setForm({ ...form, sign: e.target.value })}
          className="text-sm px-3 py-2 rounded-lg" style={inputStyle}>
          {signs.map(s => <option key={s} value={s}>{s}</option>)}
        </select>
        <input placeholder="Nepali name (e.g. मेष)" value={form.sign_ne} onChange={e => setForm({ ...form, sign_ne: e.target.value })}
          className="text-sm px-3 py-2 rounded-lg" style={inputStyle} />
        <input type="date" value={form.ad_date} onChange={e => setForm({ ...form, ad_date: e.target.value })}
          className="text-sm px-3 py-2 rounded-lg" style={inputStyle} required />
      </div>
      <textarea placeholder="Prediction (Nepali)" value={form.prediction} onChange={e => setForm({ ...form, prediction: e.target.value })}
        className="w-full text-sm px-3 py-2 rounded-lg" style={inputStyle} rows={3} required />
      <textarea placeholder="Prediction (English)" value={form.prediction_en} onChange={e => setForm({ ...form, prediction_en: e.target.value })}
        className="w-full text-sm px-3 py-2 rounded-lg" style={inputStyle} rows={2} />
      <div className="flex items-center gap-3">
        <label className="text-sm" style={{ color: "var(--muted)" }}>Rating:</label>
        <input type="number" min={1} max={5} value={form.rating} onChange={e => setForm({ ...form, rating: +e.target.value })}
          className="text-sm px-3 py-2 rounded-lg w-20" style={inputStyle} />
      </div>
      <div className="flex gap-2">
        <button type="submit" disabled={loading}
          className="px-4 py-2 rounded-lg text-sm font-bold text-white"
          style={{ background: "var(--accent)" }}>
          {loading ? "Saving..." : "Save"}
        </button>
        <button type="button" onClick={() => setShow(false)}
          className="px-4 py-2 rounded-lg text-sm" style={{ color: "var(--muted)" }}>Cancel</button>
      </div>
    </form>
  );
}
