import Link from "next/link";
import { Newspaper } from "lucide-react";

type EditorialEmptyStateProps = {
  title: string;
  description: string;
  action?: { label: string; href: string };
};

export function EditorialEmptyState({ title, description, action }: EditorialEmptyStateProps) {
  return (
    <section className="flex min-h-56 flex-col items-center justify-center border-y border-border px-6 py-12 text-center">
      <Newspaper className="mb-4 h-10 w-10 text-primary" aria-hidden="true" />
      <h2 className="text-xl font-bold text-foreground" style={{ fontFamily: "var(--font-nepali-serif)" }}>{title}</h2>
      <p className="mt-2 max-w-md text-sm leading-relaxed text-muted">{description}</p>
      {action ? <Link href={action.href} className="mt-5 text-sm font-bold text-primary underline-offset-4 hover:underline">{action.label}</Link> : null}
    </section>
  );
}
