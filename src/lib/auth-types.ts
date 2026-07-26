export type LaravelUserRole = "ADMIN" | "EDITOR" | "AUTHOR" | "READER";

export interface LaravelUser {
  id: string;
  name: string | null;
  email: string;
  image: string | null;
  role: LaravelUserRole;
  email_verified: string | null;
  session_version: number;
}

export interface LaravelSession {
  user: LaravelUser;
}
