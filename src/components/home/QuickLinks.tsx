import Link from "next/link";
import {
  ArrowUpRight,
  CalendarDays,
  ChartNoAxesCombined,
  CloudSun,
  Coins,
  Landmark,
  Sparkles,
} from "lucide-react";
import type { LucideIcon } from "lucide-react";
import { prisma } from "@/lib/prisma";

type IconKey =
  | "CalendarDays"
  | "Sparkles"
  | "ChartNoAxesCombined"
  | "Landmark"
  | "Coins"
  | "CloudSun";

const ICONS: Record<IconKey, LucideIcon> = {
  CalendarDays,
  Sparkles,
  ChartNoAxesCombined,
  Landmark,
  Coins,
  CloudSun,
};

export const dynamic = "force-dynamic";

function resolveIcon(key: string): LucideIcon {
  if (key in ICONS) return ICONS[key as IconKey];
  return CalendarDays;
}

export async function QuickLinks() {
  const links = await prisma.quickLink.findMany({
    where: { is_active: true },
    orderBy: [{ sort_order: "asc" }, { created_at: "asc" }],
    select: {
      id: true,
      slug: true,
      href: true,
      title_ne: true,
      title_en: true,
      description_ne: true,
      description_en: true,
      icon_key: true,
      accent_color: true,
      sort_order: true,
    },
  });

  const visible = links;
  const isEmpty = visible.length === 0;

  return (
    <section className="relative">
      <div className="mb-5 flex items-end justify-between gap-4">
        <div className="grid gap-1.5">
          <span className="text-[10px] font-bold uppercase tracking-[0.18em] text-muted-foreground">
            दैनिक उपकरण
          </span>
          <h3
            className="flex items-center gap-2 text-[1.1rem] font-bold text-foreground"
            style={{ fontFamily: "var(--font-nepali-serif)" }}
          >
            <span className="h-3 w-1 rounded-full bg-accent" aria-hidden="true" />
            आजका औजार
          </h3>
        </div>
        <p className="hidden text-xs font-medium text-muted sm:block sm:max-w-xs sm:text-right">
          मौसम, राशिफल, बजार र वित्त — एउटै ठाउँमा
        </p>
      </div>

      {isEmpty ? (
        <p className="rounded-md border border-dashed border-border bg-surface p-4 text-center text-sm text-muted-foreground">
          हाल कुनै द्रुत लिंक उपलब्ध छैनन्। एडमिन प्यानलबाट थप्नुहोस्।
        </p>
      ) : (
        <div className="no-scrollbar -mx-1 flex snap-x gap-3 overflow-x-auto px-1 pb-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:overflow-visible sm:px-0 lg:grid-cols-6">
          {visible.map((link) => {
            const Icon = resolveIcon(link.icon_key);
            const href =
              typeof link.href === "string" && link.href.trim().length > 0 ? link.href : "/";
            return (
              <Link
                key={link.id}
                href={href}
                className="group relative flex min-w-[11rem] snap-start flex-col gap-3 overflow-hidden rounded-lg border border-border bg-surface p-4 text-foreground shadow-xs transition-all duration-200 hover:-translate-y-0.5 hover:border-(--tool-accent) hover:shadow-md focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary sm:min-w-0"
                style={{ ["--tool-accent" as string]: link.accent_color }}
                aria-label={`${link.title_ne} — ${link.description_ne}`}
              >
                <span
                  className="absolute inset-x-0 top-0 h-0.5 origin-left scale-x-0 bg-(--tool-accent) transition-transform duration-300 group-hover:scale-x-100"
                  aria-hidden="true"
                />
                <span
                  className="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-md transition-transform duration-300 group-hover:scale-105"
                  style={{ background: `${link.accent_color}14`, color: link.accent_color }}
                  aria-hidden="true"
                >
                  <Icon className="h-5 w-5" />
                </span>
                <div className="grid gap-1">
                  <span
                    className="text-[15px] font-bold leading-snug transition-colors group-hover:text-(--tool-accent)"
                    style={{ fontFamily: "var(--font-nepali-serif)" }}
                  >
                    {link.title_ne}
                  </span>
                  <span className="text-[11px] text-muted-foreground line-clamp-2">
                    {link.description_ne}
                  </span>
                </div>
                <ArrowUpRight
                  className="absolute right-3 top-3 h-4 w-4 text-muted-foreground opacity-0 transition-all duration-200 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 group-hover:opacity-100"
                  aria-hidden="true"
                  style={{ color: link.accent_color }}
                />
              </Link>
            );
          })}
        </div>
      )}
    </section>
  );
}
