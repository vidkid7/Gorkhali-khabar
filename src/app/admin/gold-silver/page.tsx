import { prisma } from "@/lib/prisma";
import { AdminGoldSilverActions } from "./AdminGoldSilverActions";

export default async function AdminGoldSilverPage() {
  const prices = await prisma.goldSilverPrice.findMany({
    orderBy: { date: "desc" },
    take: 30,
  });

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-bold">Gold & Silver / सुन-चाँदी</h1>
      </div>

      <AdminGoldSilverActions />

      <div className="card overflow-x-auto">
        <table className="w-full text-sm">
          <thead>
            <tr style={{ borderBottom: "1px solid var(--border)" }}>
              <th className="text-left p-3 font-medium" style={{ color: "var(--muted)" }}>Date</th>
              <th className="text-right p-3 font-medium" style={{ color: "var(--muted)" }}>Gold 24k/tola</th>
              <th className="text-right p-3 font-medium" style={{ color: "var(--muted)" }}>Gold 22k/tola</th>
              <th className="text-right p-3 font-medium" style={{ color: "var(--muted)" }}>Silver/tola</th>
              <th className="text-left p-3 font-medium" style={{ color: "var(--muted)" }}>Source</th>
            </tr>
          </thead>
          <tbody>
            {prices.map((p) => (
              <tr key={p.id} style={{ borderBottom: "1px solid var(--border)" }}>
                <td className="p-3 font-medium">{new Date(p.date).toLocaleDateString()}</td>
                <td className="p-3 text-right" style={{ color: "#f59e0b" }}>
                  {p.fine_gold ? `Rs ${p.fine_gold.toLocaleString()}` : "—"}
                </td>
                <td className="p-3 text-right" style={{ color: "var(--foreground)" }}>
                  {p.tejabi_gold ? `Rs ${p.tejabi_gold.toLocaleString()}` : "—"}
                </td>
                <td className="p-3 text-right" style={{ color: "#64748b" }}>
                  {p.silver ? `Rs ${p.silver.toLocaleString()}` : "—"}
                </td>
                <td className="p-3 text-xs" style={{ color: "var(--muted)" }}>{p.source ?? "—"}</td>
              </tr>
            ))}
          </tbody>
        </table>
        {!prices.length && (
          <p className="text-center py-8 text-sm" style={{ color: "var(--muted)" }}>No prices found</p>
        )}
      </div>
    </div>
  );
}
