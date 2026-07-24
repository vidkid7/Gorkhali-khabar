export function Pagination({
  page,
  totalPages,
  pathname,
  params = {},
}: {
  page: number;
  totalPages: number;
  pathname: string;
  params?: Record<string, string>;
}) {
  if (totalPages <= 1) return null;
  const href = (target: number) => {
    const query = new URLSearchParams({ ...params, page: String(target) });
    return `${pathname}?${query.toString()}`;
  };
  return (
    <nav className="pagination" aria-label="पृष्ठहरू">
      {page > 1 && <a href={href(page - 1)}>← अघिल्लो</a>}
      <span>पृष्ठ {page} / {totalPages}</span>
      {page < totalPages && <a href={href(page + 1)}>अर्को →</a>}
    </nav>
  );
}
