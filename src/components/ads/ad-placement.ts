export function shouldInsertSectionAd(index: number): boolean {
  return (index + 1) % 3 === 0;
}

export function shouldShowInArticleAd(
  wordCount: number | null | undefined,
): boolean {
  return typeof wordCount === "number" && wordCount >= 500;
}

export function splitHtmlAtParagraph(html: string): {
  beforeAd: string;
  afterAd: string;
} {
  const boundaries = Array.from(html.matchAll(/<\/p\s*>/gi), (match) => (
    (match.index ?? 0) + match[0].length
  ));

  if (boundaries.length < 2) {
    return { beforeAd: html, afterAd: "" };
  }

  const midpoint = html.length / 2;
  const splitAt = boundaries
    .slice(0, -1)
    .reduce((closest, candidate) => (
      Math.abs(candidate - midpoint) < Math.abs(closest - midpoint)
        ? candidate
        : closest
    ));

  return {
    beforeAd: html.slice(0, splitAt),
    afterAd: html.slice(splitAt),
  };
}
