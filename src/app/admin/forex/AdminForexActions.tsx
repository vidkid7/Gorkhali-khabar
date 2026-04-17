"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";

export function AdminForexActions() {
  const router = useRouter();
  const [show, setShow] = useState(false);
  const [loading, setLoading] = useState(false);
  const [form, setForm] = useState({
    date: "", currency: "USD", currency_name: "", unit: 1, buy: 0, sell: 0,
  });

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setLoading(true);
    try {
      const res = await fetch("/api/v1/admin/forex", {
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
      + Add Rate
    </button>
  );

  const inputStyle = { background: "var(--surface)", color: "var(--foreground)", border: "1px solid var(--border)" };

  return (
    <form onSubmit={handleSubmit} className="card p-4 space-y-3">
      <h2 className="text-sm font-bold">Add Forex Rate</h2>
      <div className="grid grid-cols-2 md:grid-cols-3 gap-3">
        <input type="date" value={form.date} onChange={e => setForm({ ...form, date: e.target.value })}
          className="text-sm px-3 py-2 rounded-lg" style={inputStyle} required />
        <input placeholder="Currency Code (USD)" value={form.currency} onChange={e => setForm({ ...form, currency: e.target.value })}
          className="text-sm px-3 py-2 rounded-lg" style={inputStyle} required />
        <input placeholder="Currency Name" value={form.currency_name} onChange={e => setForm({ ...form, currency_name: e.target.value })}
          className="text-sm px-3 py-2 rounded-lg" style={inputStyle} />
        <input type="number" placeholder="Unit" value={form.unit || ""} onChange={e => setForm({ ...form, unit: +e.target.value })}
          className="text-sm px-3 py-2 rounded-lg" style={inputStyle} required />
        <input type="number" step="0.01" placeholder="Buy Rate (NPR)" value={form.buy || ""} onChange={e => setForm({ ...form, buy: +e.target.value })}
          className="text-sm px-3 py-2 rounded-lg" style={inputStyle} required />
        <input type="number" step="0.01" placeholder="Sell Rate (NPR)" value={form.sell || ""} onChange={e => setForm({ ...form, sell: +e.target.value })}
          className="text-sm px-3 py-2 rounded-lg" style={inputStyle} required />
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
