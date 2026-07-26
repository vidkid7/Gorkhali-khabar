# Next.js to Laravel API client

Use `src/lib/api/laravel.ts` for all new frontend-to-backend calls. It preserves
the existing Laravel response contract while centralizing credentials, Sanctum
CSRF, error handling, and browser/server URLs.

```ts
import { laravelApi, LaravelApiError } from "@/lib/api/laravel";

const settings = await laravelApi.get<Record<string, unknown>>(
  "/api/v1/settings",
);

try {
  await laravelApi.post("/api/v1/newsletter", { email });
} catch (error) {
  if (error instanceof LaravelApiError && error.status === 400) {
    // Render the existing form validation state from error.errors.
  }
}
```

For same-domain production hosting, leave
`NEXT_PUBLIC_LARAVEL_API_URL` empty so the browser uses relative `/api` and
`/sanctum` URLs. Set `API_INTERNAL_URL` only in the Node.js server environment
when server rendering needs a different internal/public origin. Server-only
values are never read as browser configuration.

JSON objects are serialized automatically. Pass `FormData` directly for uploads;
the browser supplies its multipart boundary. Mutating browser requests acquire
the Sanctum CSRF cookie and forward the decoded XSRF token. Server-side mutations
must explicitly forward the incoming cookie/header context or opt out only for a
deliberately stateless endpoint.
