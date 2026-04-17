import { prisma } from "@/lib/prisma";
import { AdminCategoryForm } from "@/components/admin/AdminCategoryForm";

export const dynamic = "force-dynamic";

export default async function AdminCategoriesPage() {
  const categories = await prisma.category.findMany({
    orderBy: { sort_order: "asc" },
    include: {
      _count: { select: { articles: true } },
    },
  });

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-bold">Categories</h1>
      </div>

      {/* Create Form */}
      <AdminCategoryForm />

      {/* Table */}
      <div className="card overflow-x-auto">
        <table className="w-full text-sm">
          <thead>
            <tr style={{ borderBottom: "1px solid var(--border)" }}>
              <th className="text-left p-3 font-medium" style={{ color: "var(--muted)" }}>Name</th>
              <th className="text-left p-3 font-medium" style={{ color: "var(--muted)" }}>Slug</th>
              <th className="text-left p-3 font-medium" style={{ color: "var(--muted)" }}>Color</th>
              <th className="text-left p-3 font-medium" style={{ color: "var(--muted)" }}>Sort Order</th>
              <th className="text-left p-3 font-medium" style={{ color: "var(--muted)" }}>Articles</th>
              <th className="text-left p-3 font-medium" style={{ color: "var(--muted)" }}>Active</th>
            </tr>
          </thead>
          <tbody>
            {categories.map((cat) => (
              <tr key={cat.id} style={{ borderBottom: "1px solid var(--border)" }}>
                <td className="p-3 font-medium">{cat.name}</td>
                <td className="p-3" style={{ color: "var(--muted)" }}>{cat.slug}</td>
                <td className="p-3">
                  <div className="flex items-center gap-2">
                    <span
                      className="inline-block w-5 h-5 rounded-full border"
                      style={{ background: cat.color, borderColor: "var(--border)" }}
                    />
                    <span className="text-xs" style={{ color: "var(--muted)" }}>{cat.color}</span>
                  </div>
                </td>
                <td className="p-3" style={{ color: "var(--muted)" }}>{cat.sort_order}</td>
                <td className="p-3" style={{ color: "var(--muted)" }}>{cat._count.articles}</td>
                <td className="p-3">
                  <span
                    className="inline-block px-2 py-0.5 text-xs font-semibold rounded-full"
                    style={{
                      background: cat.is_active ? "#16a34a" : "#6b7280",
                      color: "#fff",
                    }}
                  >
                    {cat.is_active ? "Active" : "Inactive"}
                  </span>
                </td>
              </tr>
            ))}
            {categories.length === 0 && (
              <tr>
                <td colSpan={6} className="p-8 text-center" style={{ color: "var(--muted)" }}>
                  No categories yet
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}
