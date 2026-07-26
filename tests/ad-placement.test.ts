import { describe, expect, it } from "vitest";
import {
  shouldInsertSectionAd,
  shouldShowInArticleAd,
  splitHtmlAtParagraph,
} from "@/components/ads/ad-placement";

describe("balanced ad placement", () => {
  it("inserts a section advertisement after every third managed section", () => {
    expect(
      Array.from({ length: 7 }, (_, index) => shouldInsertSectionAd(index)),
    ).toEqual([false, false, true, false, false, true, false]);
  });

  it("shows an in-article advertisement only for substantial articles", () => {
    expect(shouldShowInArticleAd(700)).toBe(true);
    expect(shouldShowInArticleAd(500)).toBe(true);
    expect(shouldShowInArticleAd(499)).toBe(false);
    expect(shouldShowInArticleAd(null)).toBe(false);
  });

  it("splits article HTML at the paragraph boundary nearest the middle", () => {
    const html = "<p>First paragraph.</p><p>Second paragraph is longer.</p><p>Third.</p>";

    expect(splitHtmlAtParagraph(html)).toEqual({
      beforeAd: "<p>First paragraph.</p>",
      afterAd: "<p>Second paragraph is longer.</p><p>Third.</p>",
    });
  });

  it("leaves unsplittable article HTML intact", () => {
    expect(splitHtmlAtParagraph("<p>Only one paragraph.</p>")).toEqual({
      beforeAd: "<p>Only one paragraph.</p>",
      afterAd: "",
    });
  });
});
