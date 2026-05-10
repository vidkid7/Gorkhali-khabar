import { NextResponse } from "next/server";
import type { NextRequest } from "next/server";

const adminPaths = ["/admin", "/api/v1/admin"];

// Simple in-memory rate limiter for API routes
const rateLimitMap = new Map<string, { count: number; resetTime: number }>();
const RATE_LIMIT_WINDOW = 60_000; // 1 minute
const RATE_LIMIT_MAX = 100; // max write requests per endpoint/window
const RATE_LIMIT_READ_MAX = 300; // public pages can fan out to several read APIs
const RATE_LIMIT_AUTH_MAX = 10; // stricter for auth endpoints
const RATE_LIMIT_LOGIN_MAX = 5; // credentials callback brute-force protection
const UNSAFE_METHODS = new Set(["POST", "PUT", "PATCH", "DELETE"]);

function isRateLimited(key: string, maxRequests: number): boolean {
  const now = Date.now();
  const entry = rateLimitMap.get(key);

  if (!entry || now > entry.resetTime) {
    rateLimitMap.set(key, { count: 1, resetTime: now + RATE_LIMIT_WINDOW });
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

export function proxy(request: NextRequest) {
  const { pathname } = request.nextUrl;
  const response = NextResponse.next();
  const ip = request.headers.get("x-forwarded-for")?.split(",")[0]?.trim() || "unknown";
  const isAdminPath = adminPaths.some((p) => pathname.startsWith(p));

  // ─── CSRF origin guard for state-changing API requests ───
  if (
    pathname.startsWith("/api/") &&
    !pathname.startsWith("/api/auth/") &&
    UNSAFE_METHODS.has(request.method)
  ) {
    const origin = request.headers.get("origin");
    if (origin) {
      let originHost = "";
      try {
        originHost = new URL(origin).host;
      } catch {
        return NextResponse.json({ error: "Invalid request origin" }, { status: 403 });
      }
      if (originHost !== request.nextUrl.host) {
        return NextResponse.json({ error: "Invalid request origin" }, { status: 403 });
      }
    }
  }

  // ─── Rate limiting for API routes ───
  if (pathname.startsWith("/api/")) {
    const isCredentialsLogin =
      pathname === "/api/auth/callback/credentials" && request.method === "POST";
    // Skip rate limiting for NextAuth internal routes (/api/auth/*) — they
    // call csrf, callback, and session endpoints on every login/page load,
    // which would instantly exhaust the strict 10 req/min cap.
    if (pathname.startsWith("/api/auth/") && !isCredentialsLogin) {
      // Let NextAuth handle its own security (CSRF tokens, etc.)
    } else {
      const isCustomAuthPath = pathname.startsWith("/api/v1/auth/");
      const limit = isCredentialsLogin
        ? RATE_LIMIT_LOGIN_MAX
        : isCustomAuthPath
        ? RATE_LIMIT_AUTH_MAX
        : request.method === "GET"
          ? RATE_LIMIT_READ_MAX
          : RATE_LIMIT_MAX;
      const rateLimitKey = `${ip}:${request.method}:${pathname}`;

      if (isRateLimited(rateLimitKey, limit)) {
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
  response.headers.set("X-DNS-Prefetch-Control", "off");
  response.headers.set("Referrer-Policy", "strict-origin-when-cross-origin");
  response.headers.set("Cross-Origin-Opener-Policy", "same-origin");
  response.headers.set("Cross-Origin-Resource-Policy", "same-origin");
  response.headers.set(
    "Permissions-Policy",
    "camera=(), microphone=(), geolocation=(), payment=()"
  );

  // Content-Security-Policy - Updated for Railway deployment
  const scriptSrc = ["'self'", "'unsafe-inline'", "https://accounts.google.com"];
  if (process.env.NODE_ENV !== "production") {
    scriptSrc.push("'unsafe-eval'");
  }
  const cspDirectives = [
    "default-src 'self'",
    `script-src ${scriptSrc.join(" ")}`,
    "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
    "img-src 'self' data: blob: https://res.cloudinary.com https://*.googleusercontent.com https://picsum.photos https://fastly.picsum.photos https://*.railway.app",
    "font-src 'self' https://fonts.gstatic.com",
    "connect-src 'self' https://accounts.google.com https://wttr.in https://*.railway.app",
    "frame-src https://accounts.google.com https://www.youtube.com https://www.youtube-nocookie.com",
    "frame-ancestors 'none'",
    "media-src 'self' blob: data: https:",
    "object-src 'none'",
    "base-uri 'self'",
    "form-action 'self'",
  ];
  if (process.env.NODE_ENV === "production") {
    cspDirectives.push("upgrade-insecure-requests");
  }
  response.headers.set("Content-Security-Policy", cspDirectives.join("; "));

  if (isAdminPath || pathname.startsWith("/api/auth/") || pathname.startsWith("/api/v1/auth/")) {
    response.headers.set("Cache-Control", "no-store, no-cache, must-revalidate");
    response.headers.set("Pragma", "no-cache");
  }

  if (process.env.NODE_ENV === "production") {
    response.headers.set(
      "Strict-Transport-Security",
      "max-age=31536000; includeSubDomains; preload"
    );
  }

  // ─── Admin route protection ───
  if (isAdminPath) {
    const adminIpWhitelist = (process.env.ADMIN_IP_WHITELIST || "")
      .split(",")
      .map((item) => item.trim())
      .filter(Boolean);
    if (adminIpWhitelist.length > 0 && !adminIpWhitelist.includes(ip)) {
      return pathname.startsWith("/api/")
        ? NextResponse.json({ error: "Forbidden" }, { status: 403 })
        : NextResponse.redirect(new URL("/", request.url));
    }

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
