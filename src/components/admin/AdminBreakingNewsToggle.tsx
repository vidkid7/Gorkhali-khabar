"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";

interface Props {
  id: string;
  isActive: boolean;
}

export function AdminBreakingNewsToggle({ id, isActive }: Props) {
  const router = useRouter();
  const [loading, setLoading] = useState(false);

  async function toggle() {
    setLoading(true);
    try {
      const res = await fetch(`/api/v1/admin/breaking-news/${id}`, {
        method: "PATCH",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ is_active: !isActive }),
      });
      const data = await res.json();
      if (data.success) {
        router.refresh();
      }
    } catch {
      // Silently fail
    } finally {
      setLoading(false);
    }
  }

  return (
    <button
      onClick={toggle}
      disabled={loading}
      className="px-2 py-1 text-xs rounded font-medium"
      style={{
        background: isActive ? "#dc2626" : "#16a34a",
        color: "#fff",
        opacity: loading ? 0.6 : 1,
      }}
    >
      {loading ? "..." : isActive ? "Deactivate" : "Activate"}
    </button>
  );
}
