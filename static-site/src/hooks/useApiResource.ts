import {
  useCallback,
  useEffect,
  useState,
  type DependencyList,
} from "react";

export function useApiResource<T>(
  loader: () => Promise<T>,
  dependencies: DependencyList,
) {
  const [data, setData] = useState<T | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<Error | null>(null);

  const load = useCallback(async () => {
    setLoading(true);
    setError(null);
    try {
      setData(await loader());
    } catch (cause) {
      const normalized =
        cause instanceof Error ? cause : new Error("Request failed");
      setError(() => normalized);
    } finally {
      setLoading(false);
    }
    // The caller owns the dependency list, matching React's effect convention.
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, dependencies);

  useEffect(() => {
    void load();
  }, [load]);

  return { data, loading, error, retry: load };
}
