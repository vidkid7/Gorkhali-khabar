import { redirect } from "next/navigation";
import { auth } from "@/lib/auth";
import { AdminSidebar } from "@/components/admin/AdminSidebar";
import { AdminThemeProvider } from "@/components/admin/AdminThemeProvider";

export default async function AdminLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  const session = await auth();

  if (!session?.user) {
    redirect("/auth/login");
  }

  if (session.user.role !== "ADMIN" && session.user.role !== "EDITOR") {
    redirect("/");
  }

  return (
    <AdminThemeProvider>
      <div className="flex min-h-screen" style={{ background: "var(--background)", color: "var(--foreground)" }}>
        <AdminSidebar />
        <main className="flex-1 overflow-auto">
          <div className="p-6 md:p-8 max-w-7xl mx-auto">
            {children}
          </div>
        </main>
      </div>
    </AdminThemeProvider>
  );
}
