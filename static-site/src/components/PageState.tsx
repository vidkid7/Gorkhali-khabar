export function LoadingState() {
  return (
    <div className="page-state" role="status">
      समाचार लोड हुँदैछ…
    </div>
  );
}

export function EmptyState({
  message = "सामग्री उपलब्ध छैन।",
}: {
  message?: string;
}) {
  return <div className="page-state">{message}</div>;
}

export function ErrorState({ retry }: { retry: () => void }) {
  return (
    <div className="page-state" role="alert">
      <p>सामग्री लोड गर्न सकिएन।</p>
      <button type="button" onClick={retry}>
        पुनः प्रयास गर्नुहोस्
      </button>
    </div>
  );
}

export function NotFoundPage() {
  return (
    <main className="page-state">
      <p className="eyebrow">404</p>
      <h1>पृष्ठ फेला परेन</h1>
      <a className="button-link" href="/">
        गृहपृष्ठमा फर्कनुहोस्
      </a>
    </main>
  );
}
