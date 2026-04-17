import { NextResponse } from "next/server";
import type { NextRequest } from "next/server";

const adminPaths = ["/admin", "/api/v1/admin"];

// Simple in-memory rate limiter for API routes
const rateLimitMap = new Map<string, { count: number; resetTime: number }>();
const RATE_LIMIT_WINDOW = 60_000; // 1 minute
const RATE_LIMIT_MAX = 100; // max requests per window
const RATE_LIMIT_AUTH_MAX = 10; // stricter for auth endpoints

function isRateLimited(ip: string, maxRequests: number): boolean {
  const now = Date.now();
  const entry = rateLimitMap.get(ip);

  if (!entry || now > entry.resetTime) {
    rateLimitMap.set(ip, { count: 1, resetTime: now + RATE_LIMIT_WINDOW });
    return false;
  }

  entry.count++;
  return entry.count > maxRequests;
}

// Periodically clean up expired entries (every 5 min)
if (typeof globalThis !== "undefined") {
  const cleanupKey = "__rateLimitCleanup";
  if (!(globalThis as Record<string, unknown>)[cleanupKey]) {
    (globalThis as Record<string, unknown>)[cleanupKey] = true;
    setInterval(() => {
      const now = Date.now();
      for (const [key, val] of rateLimitMap) {
        if (now > val.resetTime) rateLimitMap.delete(key);
      }
    }, 300_000);
  }
}

export function middleware(request: NextRequest) {
  const { pathname } = request.nextUrl;
  const response = NextResponse.next();
  const ip = request.headers.get("x-forwarded-for")?.split(",")[0]?.trim() || "unknown";

  // ─── Rate limiting for API routes ───
  if (pathname.startsWith("/api/")) {
    // Skip rate limiting for NextAuth internal routes (/api/auth/*) — they
    // call csrf, callback, and session endpoints on every login/page load,
    // which would instantly exhaust the strict 10 req/min cap.
    if (pathname.startsWith("/api/auth/")) {
      // Let NextAuth handle its own security (CSRF tokens, etc.)
    } else {
      const isCustomAuthPath = pathname.startsWith("/api/v1/auth/");
      const limit = isCustomAuthPath ? RATE_LIMIT_AUTH_MAX : RATE_LIMIT_MAX;

      if (isRateLimited(ip, limit)) {
        return NextResponse.json(
          { error: "Too many requests. Please try again later." },
          { status: 429 }
        );
      }
    }
  }

  // ─── Security headers ───
  response.headers.set("X-Frame-Options", "DENY");
  response.headers.set("X-Content-Type-Options", "nosniff");
  response.headers.set("X-XSS-Protection", "1; mode=block");
  response.headers.set("Referrer-Policy", "strict-origin-when-cross-origin");
  response.headers.set(
    "Permissions-Policy",
    "camera=(), microphone=(), geolocation=(), payment=()"
  );

  // Content-Security-Policy - Updated for Railway deployment
  const cspDirectives = [
    "default-src 'self'",
    "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://accounts.google.com",
    "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
    "img-src 'self' data: blob: https://res.cloudinary.com https://*.googleusercontent.com https://picsum.photos https://*.railway.app",
    "font-src 'self' https://fonts.gstatic.com",
    "connect-src 'self' https://accounts.google.com https://wttr.in https://*.railway.app",
    "frame-src https://accounts.google.com",
    "base-uri 'self'",
    "form-action 'self'",
  ];
  response.headers.set("Content-Security-Policy", cspDirectives.join("; "));

  if (process.env.NODE_ENV === "production") {
    response.headers.set(
      "Strict-Transport-Security",
      "max-age=31536000; includeSubDomains; preload"
    );
  }

  // ─── Admin route protection ───
  const isAdminPath = adminPaths.some((p) => pathname.startsWith(p));
  if (isAdminPath) {
    // Check for NextAuth session token (both secure and non-secure variants)
    const token =
      request.cookies.get("next-auth.session-token")?.value ||
      request.cookies.get("__Secure-next-auth.session-token")?.value ||
      request.cookies.get("authjs.session-token")?.value ||
      request.cookies.get("__Secure-authjs.session-token")?.value;
    
    if (!token) {
      if (pathname.startsWith("/api/")) {
        return NextResponse.json({ error: "Unauthorized" }, { status: 401 });
      }
      const loginUrl = new URL("/auth/login", request.url);
      loginUrl.searchParams.set("callbackUrl", pathname);
      return NextResponse.redirect(loginUrl);
    }
  }

  return response;
}

export const config = {
  matcher: [
    "/((?!_next/static|_next/image|favicon.ico|icons|images|manifest.json).*)",
  ],
};
