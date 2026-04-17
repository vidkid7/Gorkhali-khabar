import { v2 as cloudinary } from "cloudinary";
import { writeFile, mkdir, unlink } from "fs/promises";
import path from "path";

// Storage provider types - extensible for future providers
export type StorageProviderType = "cloudinary" | "local" | "aws" | "azure";

export interface UploadResult {
  url: string;
  publicId?: string;
  width?: number;
  height?: number;
  format?: string;
}

export interface StorageProvider {
  upload(buffer: Buffer, filename: string, mimeType: string): Promise<UploadResult>;
  delete(url: string, publicId?: string): Promise<void>;
  getPublicUrl(url: string): string;
}

// ─── Cloudinary Provider ──────────────────────────────────

class CloudinaryProvider implements StorageProvider {
  constructor() {
    cloudinary.config({
      cloud_name: process.env.CLOUDINARY_CLOUD_NAME,
      api_key: process.env.CLOUDINARY_API_KEY,
      api_secret: process.env.CLOUDINARY_API_SECRET,
    });
  }

  async upload(buffer: Buffer, filename: string, mimeType: string): Promise<UploadResult> {
    const isVideo = mimeType.startsWith("video/");
    const resourceType = isVideo ? "video" : "image";
    const folder = isVideo ? "news-portal/videos" : "news-portal/images";

    return new Promise((resolve, reject) => {
      const uploadStream = cloudinary.uploader.upload_stream(
        {
          resource_type: resourceType === "video" ? "video" : "auto",
          folder,
          public_id: filename.replace(/\.[^/.]+$/, ""),
          overwrite: true,
        },
        (error, result) => {
          if (error) return reject(error);
          if (!result) return reject(new Error("No result from Cloudinary"));
          resolve({
            url: result.secure_url,
            publicId: result.public_id,
            width: result.width,
            height: result.height,
            format: result.format,
          });
        }
      );
      uploadStream.end(buffer);
    });
  }

  async delete(_url: string, publicId?: string): Promise<void> {
    if (!publicId) {
      // Extract public_id from Cloudinary URL
      const match = _url.match(/\/upload\/(?:v\d+\/)?(.+)\.\w+$/);
      publicId = match?.[1];
    }
    if (publicId) {
      await cloudinary.uploader.destroy(publicId);
    }
  }

  getPublicUrl(url: string): string {
    return url; // Cloudinary URLs are already public
  }
}

// ─── Local Filesystem Provider ────────────────────────────

class LocalProvider implements StorageProvider {
  private uploadDir: string;

  constructor() {
    this.uploadDir = path.join(process.cwd(), "public", "uploads");
  }

  async upload(buffer: Buffer, filename: string): Promise<UploadResult> {
    await mkdir(this.uploadDir, { recursive: true });
    await writeFile(path.join(this.uploadDir, filename), buffer);
    return { url: `/uploads/${filename}` };
  }

  async delete(url: string): Promise<void> {
    try {
      const filePath = path.join(process.cwd(), "public", url);
      await unlink(filePath);
    } catch {
      // File may already be deleted
    }
  }

  getPublicUrl(url: string): string {
    return url;
  }
}

// ─── AWS S3 Provider (Stub for future use) ────────────────

class AwsS3Provider implements StorageProvider {
  async upload(_buffer: Buffer, _filename: string, _mimeType: string): Promise<UploadResult> {
    // TODO: Implement with @aws-sdk/client-s3
    // const { S3Client, PutObjectCommand } = require("@aws-sdk/client-s3");
    // const client = new S3Client({ region: process.env.AWS_S3_REGION });
    // await client.send(new PutObjectCommand({
    //   Bucket: process.env.AWS_S3_BUCKET,
    //   Key: `uploads/${_filename}`,
    //   Body: _buffer,
    //   ContentType: _mimeType,
    // }));
    throw new Error("AWS S3 storage provider not yet implemented. Install @aws-sdk/client-s3 and configure AWS credentials.");
  }

  async delete(_url: string): Promise<void> {
    throw new Error("AWS S3 storage provider not yet implemented.");
  }

  getPublicUrl(url: string): string {
    return `https://${process.env.AWS_S3_BUCKET}.s3.${process.env.AWS_S3_REGION}.amazonaws.com/${url}`;
  }
}

// ─── Azure Blob Provider (Stub for future use) ───────────

class AzureBlobProvider implements StorageProvider {
  async upload(_buffer: Buffer, _filename: string, _mimeType: string): Promise<UploadResult> {
    // TODO: Implement with @azure/storage-blob
    // const { BlobServiceClient } = require("@azure/storage-blob");
    // const client = BlobServiceClient.fromConnectionString(process.env.AZURE_STORAGE_CONNECTION_STRING);
    // const container = client.getContainerClient(process.env.AZURE_STORAGE_CONTAINER);
    // const blob = container.getBlockBlobClient(`uploads/${_filename}`);
    // await blob.uploadData(_buffer, { blobHTTPHeaders: { blobContentType: _mimeType } });
    throw new Error("Azure Blob storage provider not yet implemented. Install @azure/storage-blob and configure Azure credentials.");
  }

  async delete(_url: string): Promise<void> {
    throw new Error("Azure Blob storage provider not yet implemented.");
  }

  getPublicUrl(url: string): string {
    return url;
  }
}

// ─── Factory ──────────────────────────────────────────────

let _storageInstance: StorageProvider | null = null;

export function getStorageProvider(): StorageProvider {
  if (_storageInstance) return _storageInstance;

  const provider = (process.env.STORAGE_PROVIDER || "local") as StorageProviderType;

  switch (provider) {
    case "cloudinary":
      if (!process.env.CLOUDINARY_CLOUD_NAME || !process.env.CLOUDINARY_API_KEY || !process.env.CLOUDINARY_API_SECRET) {
        console.warn("Cloudinary credentials missing, falling back to local storage");
        _storageInstance = new LocalProvider();
      } else {
        _storageInstance = new CloudinaryProvider();
      }
      break;
    case "aws":
      _storageInstance = new AwsS3Provider();
      break;
    case "azure":
      _storageInstance = new AzureBlobProvider();
      break;
    case "local":
    default:
      _storageInstance = new LocalProvider();
      break;
  }

  return _storageInstance;
}

// Reset singleton (useful for testing)
export function resetStorageProvider(): void {
  _storageInstance = null;
}
