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
});
