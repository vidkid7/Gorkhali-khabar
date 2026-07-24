function readable(value: unknown): string | null {
  if (typeof value === "string" || typeof value === "number") return String(value);
  return null;
}

function records(value: unknown): Record<string, unknown>[] {
  if (Array.isArray(value)) return value.filter((item): item is Record<string, unknown> => !!item && typeof item === "object");
  if (value && typeof value === "object") {
    const object = value as Record<string, unknown>;
    for (const key of ["data", "items", "rates", "matches", "holidays", "records"]) {
      if (Array.isArray(object[key])) return records(object[key]);
    }
    return [object];
  }
  return [];
}

export function UtilityPanels({
  payloads,
}: {
  payloads: Array<{ endpoint: string; data: unknown }>;
}) {
  return (
    <div className="utility-panels">
      {payloads.map(({ endpoint, data }) => {
        const items = records(data);
        return (
          <section key={endpoint} className="utility-panel">
            <h2>{panelTitle(endpoint)}</h2>
            {items.length === 0 ? <p>हाल तथ्याङ्क उपलब्ध छैन।</p> : (
              <div className="utility-grid">
                {items.slice(0, 24).map((item, index) => {
                  const title = readable(item.name) || readable(item.title) || readable(item.symbol) || readable(item.currency) || `विवरण ${index + 1}`;
                  const details = Object.entries(item)
                    .filter(([key, value]) => !["id", "name", "title", "symbol", "currency"].includes(key) && readable(value))
                    .slice(0, 4);
                  return (
                    <article className="utility-card" key={String(item.id || `${endpoint}-${index}`)}>
                      <h3>{title}</h3>
                      {details.map(([key, value]) => <p key={key}><span>{labelFor(key)}</span><strong>{readable(value)}</strong></p>)}
                    </article>
                  );
                })}
              </div>
            )}
          </section>
        );
      })}
    </div>
  );
}

function panelTitle(endpoint: string) {
  if (endpoint.includes("exchange")) return "विदेशी विनिमय दर";
  if (endpoint.includes("gold")) return "सुन–चाँदी";
  if (endpoint.includes("tournaments")) return "प्रतियोगिता";
  if (endpoint.includes("matches")) return "खेल तालिका";
  if (endpoint.includes("rashifal")) return "आजको राशिफल";
  if (endpoint.includes("panchang")) return "पञ्चाङ्ग";
  if (endpoint.includes("holidays")) return "सार्वजनिक बिदा";
  if (endpoint.includes("nepse")) return "शेयर बजार";
  return "ताजा विवरण";
}

function labelFor(key: string) {
  return key.replaceAll("_", " ").replace(/\b\w/g, (letter) => letter.toUpperCase());
}
