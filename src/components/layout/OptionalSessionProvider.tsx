"use client";

import { LaravelAuthProvider } from "@/contexts/LaravelAuthContext";
import { ReactNode } from "react";

export function OptionalSessionProvider({ children }: { children: ReactNode }) {
  return <LaravelAuthProvider>{children}</LaravelAuthProvider>;
}
