import { prisma } from "@/lib/prisma";
import { AdminHolidayActions } from "./AdminHolidayActions";

export default async function AdminHolidaysPage() {
  const holidays = await prisma.holiday.findMany({
    orderBy: [{ bs_year: "desc" }, { bs_month: "asc" }, { bs_day: "asc" }],
    take: 100,
  });

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-bold">Holidays / बिदाहरू</h1>
      </div>

      <AdminHolidayActions />

      <div className="card overflow-x-auto">
        <table className="w-full text-sm">
          <thead>
            <tr style={{ borderBottom: "1px solid var(--border)" }}>
              <th className="text-left p-3 font-medium" style={{ color: "var(--muted)" }}>Title</th>
              <th className="text-left p-3 font-medium" style={{ color: "var(--muted)" }}>BS Date</th>
              <th className="text-left p-3 font-medium" style={{ color: "var(--muted)" }}>AD Date</th>
              <th className="text-left p-3 font-medium" style={{ color: "var(--muted)" }}>Type</th>
              <th className="text-left p-3 font-medium" style={{ color: "var(--muted)" }}>Public</th>
            </tr>
          </thead>
          <tbody>
            {holidays.map((h) => (
              <tr key={h.id} style={{ borderBottom: "1px solid var(--border)" }}>
                <td className="p-3">
                  <p className="font-medium">{h.title}</p>
                  {h.title_en && <p className="text-xs" style={{ color: "var(--muted)" }}>{h.title_en}</p>}
                </td>
                <td className="p-3" style={{ color: "var(--muted)" }}>{h.bs_year}/{h.bs_month}/{h.bs_day}</td>
                <td className="p-3" style={{ color: "var(--muted)" }}>{new Date(h.ad_date).toLocaleDateString()}</td>
                <td className="p-3">
                  <span className="px-2 py-0.5 text-xs font-semibold rounded-full"
                    style={{
                      background: h.type === "public" ? "rgba(220,38,38,0.1)" : "rgba(245,158,11,0.1)",
                      color: h.type === "public" ? "#dc2626" : "#f59e0b",
                    }}>
                    {h.type}
                  </span>
                </td>
                <td className="p-3">
                  <span className="inline-block px-2 py-0.5 text-xs font-semibold rounded-full"
                    style={{ background: h.is_public ? "#16a34a" : "#6b7280", color: "#fff" }}>
                    {h.is_public ? "Yes" : "No"}
                  </span>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
        {!holidays.length && (
          <p className="text-center py-8 text-sm" style={{ color: "var(--muted)" }}>No holidays found</p>
        )}
      </div>
    </div>
  );
}
