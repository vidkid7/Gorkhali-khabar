import { render, screen } from "@testing-library/react";
import { describe, expect, it } from "vitest";
import { LanguageProvider } from "@/contexts/LanguageContext";
import { BreakingNewsTicker } from "@/components/ui/BreakingNewsTicker";

const breakingItem = {
  id: "breaking-one",
  title: "गोर्खाली खबर विशेष अपडेट",
  title_en: "Gorkhali Khabar Special Update",
  article: { slug: "seeded-editorial-lead" },
};

function renderTicker() {
  return render(
    <LanguageProvider>
      <BreakingNewsTicker items={[breakingItem]} />
    </LanguageProvider>,
  );
}

describe("BreakingNewsTicker", () => {
  it("duplicates a single item so the animated track remains continuous", () => {
    renderTicker();

    expect(screen.getAllByText(breakingItem.title)).toHaveLength(2);
  });

  it("keeps the breaking label outside the moving track", () => {
    renderTicker();

    expect(screen.getByTestId("breaking-label")).not.toHaveClass("breaking-news-track");
    expect(screen.getByTestId("breaking-track")).toHaveClass("breaking-news-track");
  });
});
