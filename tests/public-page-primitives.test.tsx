import { render, screen } from "@testing-library/react";
import { describe, expect, it } from "vitest";
import { PublicPageHeader } from "@/components/ui/PublicPageHeader";
import { EditorialEmptyState } from "@/components/ui/EditorialEmptyState";

describe("public page primitives", () => {
  it("renders one page heading and linked breadcrumbs", () => {
    render(
      <PublicPageHeader
        title="राजनीति"
        breadcrumbs={[{ label: "गृहपृष्ठ", href: "/" }, { label: "राजनीति" }]}
      />,
    );
    expect(screen.getByRole("heading", { level: 1, name: "राजनीति" })).toBeInTheDocument();
    expect(screen.getByRole("link", { name: "गृहपृष्ठ" })).toHaveAttribute("href", "/");
  });

  it("renders a recovery action when provided", () => {
    render(
      <EditorialEmptyState
        title="समाचार भेटिएन"
        description="अर्को विषय हेर्नुहोस्।"
        action={{ label: "गृहपृष्ठ", href: "/" }}
      />,
    );
    expect(screen.getByRole("link", { name: "गृहपृष्ठ" })).toHaveAttribute("href", "/");
  });
});
