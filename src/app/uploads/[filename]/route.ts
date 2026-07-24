import { readFile, stat } from "fs/promises";
import { NextRequest, NextResponse } from "next/server";
import { resolveLocalUploadPath } from "@/lib/storage";

export const dynamic = "force-dynamic";

const contentTypes: Record<string, string> = {
  avif: "image/avif",
  gif: "image/gif",
  jpg: "image/jpeg",
  jpeg: "image/jpeg",
  pdf: "application/pdf",
  png: "image/png",
  webm: "video/webm",
  webp: "image/webp",
  mp4: "video/mp4",
};

function contentTypeFor(filename: string) {
  const ext = filename.split(".").pop()?.toLowerCase() ?? "";
  return contentTypes[ext] ?? "application/octet-stream";
}

async function localUploadResponse(filename: string, includeBody: boolean) {
  const filePath = resolveLocalUploadPath(filename);
  if (!filePath) return new NextResponse("Not found", { status: 404 });

  try {
    const fileStat = await stat(filePath);
    const headers: Record<string, string> = {
      "Cache-Control": "public, max-age=31536000, immutable",
      "Content-Length": String(fileStat.size),
      "Content-Type": contentTypeFor(filename),
    };

    if (!includeBody) return new NextResponse(null, { status: 200, headers });

    const file = await readFile(filePath);
    return new NextResponse(file, { status: 200, headers });
  } catch {
    return new NextResponse("Not found", { status: 404 });
  }
}

export async function GET(
  _request: NextRequest,
  { params }: { params: Promise<{ filename: string }> }
) {
  const { filename } = await params;
  return localUploadResponse(filename, true);
}

export async function HEAD(
  _request: NextRequest,
  { params }: { params: Promise<{ filename: string }> }
) {
  const { filename } = await params;
  return localUploadResponse(filename, false);
}
