import { render, screen } from "@testing-library/react";
import { describe, expect, it } from "vitest";
import { SiteHeader } from "./SiteHeader";

describe("SiteHeader", () => {
  it("uses the complete raster wordmark", () => {
    render(<SiteHeader />);
    expect(screen.getByAltText("Gorkhali Khabar")).toHaveAttribute(
      "src",
      "/icons/logo.png",
    );
  });

  it("keeps the public header admin-free and includes home and patro navigation", () => {
    render(<SiteHeader />);

    expect(screen.getByRole("link", { name: "गृहपृष्ठ" })).toHaveAttribute("href", "/");
    expect(screen.getByRole("link", { name: "पात्रो" })).toHaveAttribute("href", "/patro");
    expect(screen.queryByRole("link", { name: "सम्पादकीय प्रवेश" })).not.toBeInTheDocument();
  });
});
