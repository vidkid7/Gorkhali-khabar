import { prisma } from "@/lib/prisma";
import { AdminRashifalActions } from "./AdminRashifalActions";

export default async function AdminRashifalPage() {
  const entries = await prisma.rashifal.findMany({
    orderBy: [{ ad_date: "desc" }, { sign: "asc" }],
    take: 24,
  });

  const signs = ["mesh", "brish", "mithun", "karkat", "simha", "kanya", "tula", "brishchik", "dhanu", "makar", "kumbha", "meen"];
  const EMOJIS: Record<string, string> = {
    mesh: "♈", brish: "♉", mithun: "♊", karkat: "♋", simha: "♌", kanya: "♍",
    tula: "♎", brishchik: "♏", dhanu: "♐", makar: "♑", kumbha: "♒", meen: "♓",
  };

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-bold">Rashifal / राशिफल</h1>
      </div>

      <AdminRashifalActions signs={signs} />

      <div className="card overflow-x-auto">
        <table className="w-full text-sm">
          <thead>
            <tr style={{ borderBottom: "1px solid var(--border)" }}>
              <th className="text-left p-3 font-medium" style={{ color: "var(--muted)" }}>Sign</th>
              <th className="text-left p-3 font-medium" style={{ color: "var(--muted)" }}>Date</th>
              <th className="text-left p-3 font-medium" style={{ color: "var(--muted)" }}>Prediction</th>
              <th className="text-center p-3 font-medium" style={{ color: "var(--muted)" }}>Rating</th>
            </tr>
          </thead>
          <tbody>
            {entries.map((r) => (
              <tr key={r.id} style={{ borderBottom: "1px solid var(--border)" }}>
                <td className="p-3 font-medium whitespace-nowrap">
                  <span className="mr-1">{EMOJIS[r.sign] ?? "⭐"}</span>
                  {r.sign_ne ?? r.sign}
                </td>
                <td className="p-3 whitespace-nowrap" style={{ color: "var(--muted)" }}>
                  {new Date(r.ad_date).toLocaleDateString()}
                </td>
                <td className="p-3 max-w-md truncate" style={{ color: "var(--foreground)" }}>
                  {r.prediction.slice(0, 80)}...
                </td>
                <td className="p-3 text-center">
                  {r.rating ? "⭐".repeat(r.rating) : "—"}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
        {!entries.length && (
          <p className="text-center py-8 text-sm" style={{ color: "var(--muted)" }}>No rashifal entries found</p>
        )}
      </div>
    </div>
  );
}
