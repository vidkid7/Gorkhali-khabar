export type LaravelValidationErrors = Record<string, string[]>;

export type LaravelApiEnvelope<T> =
  | {
      success: true;
      data?: T;
      message?: string;
    }
  | {
      success: false;
      error: string;
      errors?: LaravelValidationErrors;
    };

export class LaravelApiError extends Error {
  readonly status: number;
  readonly errors?: LaravelValidationErrors;

  constructor(
    message: string,
    status: number,
    errors?: LaravelValidationErrors,
  ) {
    super(message);
    this.name = "LaravelApiError";
    this.status = status;
    this.errors = errors;
  }
}

export interface LaravelApiBaseUrlOptions {
  server: boolean;
  internalUrl?: string;
  publicUrl?: string;
}

export function resolveLaravelApiBaseUrl({
  server,
  internalUrl,
  publicUrl,
}: LaravelApiBaseUrlOptions): string {
  const selected = server ? internalUrl || publicUrl : publicUrl;
  return selected?.replace(/\/+$/, "") ?? "";
}

export interface LaravelApiClientOptions {
  baseUrl?: string;
  fetcher?: typeof fetch;
  readXsrfToken?: () => string | undefined;
}

export interface LaravelRequestOptions
  extends Omit<RequestInit, "body" | "method"> {
  csrf?: boolean;
}

type JsonBody = Record<string, unknown> | unknown[];
type LaravelRequestBody = BodyInit | JsonBody | null | undefined;

const mutatingMethods = new Set(["POST", "PUT", "PATCH", "DELETE"]);

function defaultXsrfTokenReader(): string | undefined {
  if (typeof document === "undefined") {
    return undefined;
  }

  const cookie = document.cookie
    .split("; ")
    .find((entry) => entry.startsWith("XSRF-TOKEN="));
  const value = cookie?.slice("XSRF-TOKEN=".length);

  if (!value) {
    return undefined;
  }

  return value;
}

function decodeXsrfToken(value: string): string {
  try {
    return decodeURIComponent(value);
  } catch {
    return value;
  }
}

function defaultBaseUrl(): string {
  return resolveLaravelApiBaseUrl({
    server: typeof window === "undefined",
    internalUrl: process.env.API_INTERNAL_URL,
    publicUrl: process.env.NEXT_PUBLIC_LARAVEL_API_URL,
  });
}

function joinUrl(baseUrl: string, path: string): string {
  if (/^https?:\/\//i.test(path)) {
    return path;
  }

  const normalizedPath = path.startsWith("/") ? path : `/${path}`;
  return `${baseUrl}${normalizedPath}`;
}

function isJsonBody(body: LaravelRequestBody): body is JsonBody {
  if (body === null || body === undefined) {
    return false;
  }

  if (typeof FormData !== "undefined" && body instanceof FormData) {
    return false;
  }
  if (typeof Blob !== "undefined" && body instanceof Blob) {
    return false;
  }
  if (typeof URLSearchParams !== "undefined" && body instanceof URLSearchParams) {
    return false;
  }
  if (typeof ArrayBuffer !== "undefined" && body instanceof ArrayBuffer) {
    return false;
  }
  if (ArrayBuffer.isView(body)) {
    return false;
  }
  if (typeof ReadableStream !== "undefined" && body instanceof ReadableStream) {
    return false;
  }

  return typeof body === "object";
}

async function parseResponseBody(response: Response): Promise<unknown> {
  const text = await response.text();

  if (!text) {
    return undefined;
  }

  try {
    return JSON.parse(text);
  } catch {
    return text;
  }
}

export function createLaravelApiClient({
  baseUrl = defaultBaseUrl(),
  fetcher = fetch,
  readXsrfToken = defaultXsrfTokenReader,
}: LaravelApiClientOptions = {}) {
  const normalizedBaseUrl = baseUrl.replace(/\/+$/, "");
  let csrfPromise: Promise<void> | undefined;

  async function ensureCsrfCookie(): Promise<void> {
    csrfPromise ??= (async () => {
      const response = await fetcher(
        joinUrl(normalizedBaseUrl, "/sanctum/csrf-cookie"),
        {
          method: "GET",
          credentials: "include",
          headers: { Accept: "application/json" },
        },
      );

      if (!response.ok) {
        throw new LaravelApiError(
          "Unable to initialize a secure session.",
          response.status,
        );
      }
    })().catch((error) => {
      csrfPromise = undefined;
      throw error;
    });

    return csrfPromise;
  }

  async function request<T>(
    method: string,
    path: string,
    body?: LaravelRequestBody,
    options: LaravelRequestOptions = {},
  ): Promise<T> {
    const upperMethod = method.toUpperCase();
    const shouldUseCsrf =
      options.csrf !== false &&
      mutatingMethods.has(upperMethod) &&
      (typeof document !== "undefined" || readXsrfToken !== defaultXsrfTokenReader);

    try {
      if (shouldUseCsrf) {
        await ensureCsrfCookie();
      }

      const headers = new Headers(options.headers);
      headers.set("Accept", "application/json");

      let requestBody = body as BodyInit | null | undefined;
      if (isJsonBody(body)) {
        headers.set("Content-Type", "application/json");
        requestBody = JSON.stringify(body);
      }

      const xsrfToken = shouldUseCsrf ? readXsrfToken() : undefined;
      if (xsrfToken) {
        headers.set("X-XSRF-TOKEN", decodeXsrfToken(xsrfToken));
      }

      const requestOptions = { ...options };
      delete requestOptions.csrf;
      const response = await fetcher(joinUrl(normalizedBaseUrl, path), {
        ...requestOptions,
        method: upperMethod,
        credentials: options.credentials ?? "include",
        headers,
        body: requestBody,
      });
      const payload = await parseResponseBody(response);

      if (!response.ok) {
        const failure =
          payload && typeof payload === "object"
            ? (payload as Partial<Extract<LaravelApiEnvelope<never>, { success: false }>>)
            : undefined;
        throw new LaravelApiError(
          failure?.error || "Request failed.",
          response.status,
          failure?.errors,
        );
      }

      if (
        payload &&
        typeof payload === "object" &&
        "success" in payload &&
        (payload as { success?: boolean }).success === true
      ) {
        return (payload as Extract<LaravelApiEnvelope<T>, { success: true }>).data as T;
      }

      return payload as T;
    } catch (error) {
      if (error instanceof LaravelApiError) {
        throw error;
      }

      throw new LaravelApiError("Unable to reach the server.", 0);
    }
  }

  return {
    request,
    get: <T>(path: string, options?: LaravelRequestOptions) =>
      request<T>("GET", path, undefined, options),
    post: <T>(
      path: string,
      body?: LaravelRequestBody,
      options?: LaravelRequestOptions,
    ) => request<T>("POST", path, body, options),
    put: <T>(
      path: string,
      body?: LaravelRequestBody,
      options?: LaravelRequestOptions,
    ) => request<T>("PUT", path, body, options),
    patch: <T>(
      path: string,
      body?: LaravelRequestBody,
      options?: LaravelRequestOptions,
    ) => request<T>("PATCH", path, body, options),
    delete: <T>(path: string, options?: LaravelRequestOptions) =>
      request<T>("DELETE", path, undefined, options),
    ensureCsrfCookie,
  };
}

export const laravelApi = createLaravelApiClient();
