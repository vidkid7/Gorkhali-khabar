import type { SupportedLanguage } from "@/types";

/**
 * Merge CSS class names, filtering out falsy values.
 */
export function cn(...classes: (string | false | null | undefined)[]): string {
  return classes.filter(Boolean).join(" ");
}

/**
 * Format a relative time string (e.g., "2 hours ago").
 * Supports both Nepali and English output.
 */
export function timeAgo(date: Date | string, language: SupportedLanguage = "ne"): string {
  const now = new Date();
  const then = new Date(date);
  const diffMs = now.getTime() - then.getTime();
  const diffMins = Math.floor(diffMs / 60000);
  const diffHours = Math.floor(diffMs / 3600000);
  const diffDays = Math.floor(diffMs / 86400000);

  if (language === "ne") {
    const toNep = (n: number) =>
      String(n).replace(/\d/g, (d) => "०१२३४५६७८९"[parseInt(d)]);
    if (diffMins < 1) return "अहिले भर्खरै";
    if (diffMins < 60) return `${toNep(diffMins)} मिनेट अगाडि`;
    if (diffHours < 24) return `${toNep(diffHours)} घण्टा अगाडि`;
    if (diffDays < 7) return `${toNep(diffDays)} दिन अगाडि`;
    return formatStableDate(then, language);
  }

  if (diffMins < 1) return "Just now";
  if (diffMins < 60) return `${diffMins} min ago`;
  if (diffHours < 24) return `${diffHours}h ago`;
  if (diffDays < 7) return `${diffDays}d ago`;
  return formatStableDate(then, language);
}

function formatStableDate(date: Date, language: SupportedLanguage): string {
  const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
  const year = date.getUTCFullYear();
  const month = date.getUTCMonth() + 1;
  const day = date.getUTCDate();

  if (language === "ne") {
    const toNep = (value: number | string) =>
      String(value).replace(/\d/g, (d) => "०१२३४५६७८९"[parseInt(d)]);
    return `${toNep(year)}-${toNep(String(month).padStart(2, "0"))}-${toNep(String(day).padStart(2, "0"))}`;
  }

  return `${months[month - 1]} ${day}, ${year}`;
}

/**
 * Generate a URL-safe slug from a string.
 */
export function slugify(text: string): string {
  return text
    .toLowerCase()
    .trim()
    .replace(/[^\w\s-]/g, "")
    .replace(/[\s_]+/g, "-")
    .replace(/-+/g, "-")
    .replace(/^-+|-+$/g, "");
}

/**
 * Format a number for display (with locale-aware formatting).
 */
export function formatNumber(num: number, language: SupportedLanguage = "ne"): string {
  if (language === "ne") {
    return String(num).replace(/\d/g, (d) => "०१२३४५६७८९"[parseInt(d)]);
  }
  return num.toLocaleString("en-US");
}

/**
 * Truncate text to a maximum length, adding ellipsis.
 */
export function truncate(text: string, maxLength: number): string {
  if (text.length <= maxLength) return text;
  return text.slice(0, maxLength).trimEnd() + "…";
}

/**
 * Generate initials from a name (e.g., "John Doe" → "JD").
 */
export function getInitials(name: string): string {
  return name
    .split(" ")
    .map((part) => part[0])
    .filter(Boolean)
    .slice(0, 2)
    .join("")
    .toUpperCase();
}

/**
 * Status color configuration for consistent use across components.
 */
export const STATUS_COLORS = {
  DRAFT: { bg: "var(--muted)", text: "#fff", label: "Draft", labelNe: "ड्राफ्ट" },
  PUBLISHED: { bg: "var(--success)", text: "#fff", label: "Published", labelNe: "प्रकाशित" },
  ARCHIVED: { bg: "var(--warning)", text: "#fff", label: "Archived", labelNe: "संग्रहित" },
  PENDING: { bg: "var(--warning)", text: "#fff", label: "Pending", labelNe: "पेन्डिङ" },
  APPROVED: { bg: "var(--success)", text: "#fff", label: "Approved", labelNe: "स्वीकृत" },
  REJECTED: { bg: "var(--error)", text: "#fff", label: "Rejected", labelNe: "अस्वीकृत" },
  SPAM: { bg: "var(--error)", text: "#fff", label: "Spam", labelNe: "स्प्याम" },
  LIVE: { bg: "var(--error)", text: "#fff", label: "LIVE", labelNe: "लाइभ" },
  UPCOMING: { bg: "var(--primary)", text: "#fff", label: "Upcoming", labelNe: "आगामी" },
  COMPLETED: { bg: "var(--muted)", text: "#fff", label: "FT", labelNe: "समाप्त" },
  CANCELLED: { bg: "var(--muted)", text: "#fff", label: "Cancelled", labelNe: "रद्द" },
} as const;

export type StatusKey = keyof typeof STATUS_COLORS;

/**
 * Design system category colors for consistent theming.
 * Maps category slugs to design system colors.
 * Falls back to database color if slug not found.
 */
export const CATEGORY_COLORS: Record<string, string> = {
  // Core news categories
  'samachar': 'var(--accent)',           // Red for breaking/latest news
  'rajniti': 'var(--accent)',            // Red for politics
  'arthatantra': '#2e7d32',              // Green for economy (Material Green 800)
  'khelkud': '#e65100',                  // Orange for sports (Material Orange 900)
  'prabidhi': '#6a1b9a',                 // Purple for technology (Material Purple 900)
  'swasthya': '#00695c',                 // Teal for health (Material Teal 900)
  'shiksha': 'var(--primary)',           // Blue for education
  'sahitya': '#6d4c41',                  // Brown for literature (Material Brown 700)
  'antarrashtriya': '#283593',           // Indigo for international (Material Indigo 900)
  'bichar': '#00838f',                   // Cyan for opinion/analysis (Material Cyan 700)
  
  // Feature/special categories
  'feature': '#ad1457',                  // Pink for features (Material Pink 800)
  'cover-story': '#bf360c',              // Deep orange for cover stories (Material Deep Orange 900)
  'antarvaarta': '#4e342e',              // Brown for interviews (Material Brown 800)
  'saptaahanta': '#558b2f',              // Light green for weekend (Material Light Green 800)
  'bichitra': '#f57f17',                 // Amber for miscellaneous (Material Amber 900)
  
  // Media categories
  'video': '#d50000',                    // Red for video
  'photo-gallery': '#0097a7',            // Cyan for photo gallery
  'reels': '#c51162',                    // Pink for reels
  
  // Special
  'breaking': 'var(--accent)',           // Red for breaking news
};

/**
 * Get category color with fallback to database color.
 * Uses design system colors for known slugs, falls back to provided color.
 */
export function getCategoryColorWithFallback(slug: string, fallbackColor: string): string {
  return CATEGORY_COLORS[slug] ?? fallbackColor;
}

/**
 * Get category color with CSS variable fallback.
 * Returns CSS variable if available, otherwise hex color.
 */
export function getCategoryColor(slug: string): string {
  return CATEGORY_COLORS[slug] ?? 'var(--primary)';
}
