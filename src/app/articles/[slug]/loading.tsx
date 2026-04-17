export default function ArticleLoading() {
  return (
    <div className="mx-auto max-w-4xl px-4 py-6 animate-pulse">
      {/* Breadcrumb */}
      <div className="h-4 w-48 bg-[var(--surface)] rounded mb-4" />
      {/* Title */}
      <div className="space-y-3 mb-6">
        <div className="h-8 w-full bg-[var(--surface)] rounded" />
        <div className="h-8 w-3/4 bg-[var(--surface)] rounded" />
      </div>
      {/* Meta */}
      <div className="flex gap-3 mb-6">
        <div className="h-5 w-20 bg-[var(--surface)] rounded-full" />
        <div className="h-5 w-32 bg-[var(--surface)] rounded" />
        <div className="h-5 w-24 bg-[var(--surface)] rounded" />
      </div>
      {/* Featured image */}
      <div className="w-full h-64 md:h-96 bg-[var(--surface)] rounded-lg mb-6" />
      {/* Content lines */}
      <div className="space-y-3">
        {Array.from({ length: 12 }).map((_, i) => (
          <div key={i} className="h-4 bg-[var(--surface)] rounded" style={{ width: `${75 + Math.random() * 25}%` }} />
        ))}
      </div>
    </div>
  );
}
