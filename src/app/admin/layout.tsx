import { redirect } from "next/navigation";
import { requireRole } from "@/lib/auth-helpers";
import { AdminSidebar } from "@/components/admin/AdminSidebar";
import { AdminThemeProvider } from "@/components/admin/AdminThemeProvider";

export default async function AdminLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  const { error } = await requireRole(["ADMIN", "EDITOR"]);

  if (error === "unauthorized") {
    redirect("/auth/login");
  }

  if (error === "forbidden") {
    redirect("/");
  }

  return (
    <AdminThemeProvider>
      <div className="flex min-h-screen overflow-x-hidden" style={{ background: "var(--background)", color: "var(--foreground)" }}>
        <AdminSidebar />
        <main className="flex-1 min-w-0 overflow-auto">
          <div className="mx-auto max-w-7xl px-4 pb-6 pt-20 sm:px-6 md:p-8">
            {children}
          </div>
        </main>
      </div>
    </AdminThemeProvider>
  );
}
