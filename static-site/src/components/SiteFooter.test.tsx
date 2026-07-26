import { render, screen } from "@testing-library/react";
import { describe, expect, it } from "vitest";
import { SiteFooter } from "./SiteFooter";

describe("SiteFooter", () => {
  it("identifies AashaTech as the site manager", () => {
    render(<SiteFooter />);

    expect(screen.getByText("Managed by AashaTech")).toBeInTheDocument();
  });
});
