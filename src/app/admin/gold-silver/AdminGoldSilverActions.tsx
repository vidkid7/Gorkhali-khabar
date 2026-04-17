"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";

export function AdminGoldSilverActions() {
  const router = useRouter();
  const [show, setShow] = useState(false);
  const [loading, setLoading] = useState(false);
  const [form, setForm] = useState({
    date: "", fine_gold: 0, tejabi_gold: 0, silver: 0, source: "FENEGOSIDA",
  });

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setLoading(true);
    try {
      const res = await fetch("/api/v1/admin/gold-silver", {
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
      + Add Price
    </button>
  );

  const inputStyle = { background: "var(--surface)", color: "var(--foreground)", border: "1px solid var(--border)" };

  return (
    <form onSubmit={handleSubmit} className="card p-4 space-y-3">
      <h2 className="text-sm font-bold">Add Gold/Silver Price</h2>
      <div className="grid grid-cols-1 md:grid-cols-3 gap-3">
        <input type="date" value={form.date} onChange={e => setForm({ ...form, date: e.target.value })}
          className="text-sm px-3 py-2 rounded-lg" style={inputStyle} required />
        <input type="number" placeholder="Fine Gold/tola" value={form.fine_gold || ""} onChange={e => setForm({ ...form, fine_gold: +e.target.value })}
          className="text-sm px-3 py-2 rounded-lg" style={inputStyle} required />
        <input type="number" placeholder="Tejabi Gold/tola" value={form.tejabi_gold || ""} onChange={e => setForm({ ...form, tejabi_gold: +e.target.value })}
          className="text-sm px-3 py-2 rounded-lg" style={inputStyle} required />
        <input type="number" placeholder="Silver/tola" value={form.silver || ""} onChange={e => setForm({ ...form, silver: +e.target.value })}
          className="text-sm px-3 py-2 rounded-lg" style={inputStyle} required />
        <input placeholder="Source" value={form.source} onChange={e => setForm({ ...form, source: e.target.value })}
          className="text-sm px-3 py-2 rounded-lg" style={inputStyle} />
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
