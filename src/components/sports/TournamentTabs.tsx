"use client";

import { useState } from "react";

interface Tournament {
  id: string;
  name: string;
  slug: string;
  sport_type: string;
}

interface TournamentTabsProps {
  tournaments: Tournament[];
  activeId: string | null;
  onSelect: (id: string | null) => void;
}

export function TournamentTabs({ tournaments, activeId, onSelect }: TournamentTabsProps) {
  const [scrollLeft, setScrollLeft] = useState(false);

  return (
    <div className="relative" role="tablist" aria-label="Tournament">
      <div
        className="flex gap-2 overflow-x-auto pb-2 scrollbar-hide"
        onScroll={(e) => setScrollLeft(e.currentTarget.scrollLeft > 0)}
      >
        <button
          onClick={() => onSelect(null)}
          role="tab" aria-selected={activeId === null}
          className="shrink-0 border-b-2 px-4 py-2 text-sm font-medium transition-colors"
          style={{
            background: "transparent",
            color: activeId === null ? "var(--primary)" : "var(--foreground)",
            borderColor: activeId === null ? "var(--primary)" : "transparent",
          }}
        >
          सबै
        </button>
        {tournaments.map((t) => (
          <button
            key={t.id}
            onClick={() => onSelect(t.id)}
            role="tab" aria-selected={activeId === t.id}
            className="shrink-0 border-b-2 px-4 py-2 text-sm font-medium transition-colors"
            style={{
              background: "transparent",
              color: activeId === t.id ? "var(--primary)" : "var(--foreground)",
              borderColor: activeId === t.id ? "var(--primary)" : "transparent",
            }}
          >
            {t.name}
          </button>
        ))}
      </div>
      {scrollLeft && (
        <div
          className="absolute left-0 top-0 bottom-2 w-8 pointer-events-none"
          style={{ background: "linear-gradient(to right, var(--background), transparent)" }}
        />
      )}
    </div>
  );
}
