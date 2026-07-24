import Link from "next/link";
import type { CSSProperties, ReactNode } from "react";
import { ChevronRight } from "lucide-react";

export type PublicPageHeaderProps = {
  title: string;
  eyebrow?: string;
  description?: string | null;
  countLabel?: string;
  breadcrumbs?: Array<{ label: string; href?: string }>;
  actions?: ReactNode;
  accentColor?: string;
};

export function PublicPageHeader({
  title,
  eyebrow,
  description,
  countLabel,
  breadcrumbs,
  actions,
  accentColor = "var(--primary)",
}: PublicPageHeaderProps) {
  return (
    <header className="public-page-header" style={{ "--page-accent": accentColor } as CSSProperties}>
      {breadcrumbs?.length ? (
        <nav aria-label="Breadcrumb" className="mb-4 text-sm text-muted">
          <ol className="flex flex-wrap items-center gap-1.5">
            {breadcrumbs.map((breadcrumb, index) => (
              <li key={`${breadcrumb.label}-${index}`} className="flex items-center gap-1.5">
                {index > 0 && <ChevronRight className="h-3.5 w-3.5" aria-hidden="true" />}
                {breadcrumb.href ? <Link href={breadcrumb.href} className="hover:text-primary">{breadcrumb.label}</Link> : <span className="font-medium text-foreground">{breadcrumb.label}</span>}
              </li>
            ))}
          </ol>
        </nav>
      ) : null}
      <div className="flex flex-wrap items-end justify-between gap-4 border-b border-border pb-4">
        <div className="min-w-0">
          {eyebrow ? <p className="editorial-kicker mb-2" style={{ color: "var(--page-accent)" }}>{eyebrow}</p> : null}
          <h1 className="text-3xl font-bold leading-[1.45] text-foreground sm:text-4xl" style={{ fontFamily: "var(--font-nepali-serif)" }}>{title}</h1>
          {description ? <p className="mt-2 max-w-3xl text-sm leading-relaxed text-muted">{description}</p> : null}
          {countLabel ? <p className="mt-2 text-sm text-muted">{countLabel}</p> : null}
        </div>
        {actions ? <div className="shrink-0">{actions}</div> : null}
      </div>
      <span className="block h-[3px] w-16" style={{ backgroundColor: "var(--page-accent)" }} />
    </header>
  );
}
