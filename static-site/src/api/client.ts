interface ApiEnvelope<T> {
  success: boolean;
  data?: T;
  error?: string;
  message?: string;
}

export class ApiRequestError extends Error {
  constructor(
    public readonly status: number,
    message: string,
  ) {
    super(message);
    this.name = "ApiRequestError";
  }
}

export async function apiGet<T>(
  path: string,
  init: RequestInit = {},
): Promise<T> {
  const response = await fetch(path, {
    ...init,
    headers: {
      Accept: "application/json",
      ...init.headers,
    },
  });

  let body: ApiEnvelope<T>;
  try {
    body = (await response.json()) as ApiEnvelope<T>;
  } catch {
    throw new ApiRequestError(response.status, "Unable to load content.");
  }

  if (!response.ok || !body.success || body.data === undefined) {
    throw new ApiRequestError(
      response.status,
      body.error || body.message || "Unable to load content.",
    );
  }

  return body.data;
}

export async function apiGetOptionalArray<T>(path: string): Promise<T[]> {
  try {
    const data = await apiGet<T[] | undefined>(path);
    return Array.isArray(data) ? data : [];
  } catch (error) {
    if (error instanceof ApiRequestError && error.status === 404) return [];
    throw error;
  }
}
