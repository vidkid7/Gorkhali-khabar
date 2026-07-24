import { renderHook, waitFor } from "@testing-library/react";
import { describe, expect, it, vi } from "vitest";
import { useApiResource } from "./useApiResource";

describe("useApiResource", () => {
  it("exposes loading then data", async () => {
    const loader = vi.fn().mockResolvedValue({ value: 7 });
    const { result } = renderHook(() => useApiResource(loader, []));
    expect(result.current.loading).toBe(true);
    await waitFor(() => expect(result.current.data).toEqual({ value: 7 }));
    expect(result.current.error).toBeNull();
  });

  it("captures loader errors", async () => {
    const loader = vi.fn().mockRejectedValue(new Error("offline"));
    const { result } = renderHook(() => useApiResource(loader, []));
    await waitFor(() => expect(result.current.loading).toBe(false));
    expect(result.current.error?.message).toBe("offline");
  });
});
