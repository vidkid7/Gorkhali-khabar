import { z } from "zod";

export const registerSchema = z.object({
  name: z.string().min(2, "नाम कम्तीमा २ अक्षरको हुनुपर्छ"),
  email: z.string().email("मान्य इमेल ठेगाना प्रविष्ट गर्नुहोस्"),
  password: z
    .string()
    .min(8, "पासवर्ड कम्तीमा ८ अक्षरको हुनुपर्छ")
    .regex(/[A-Z]/, "कम्तीमा एउटा ठूलो अक्षर चाहिन्छ")
    .regex(/[a-z]/, "कम्तीमा एउटा सानो अक्षर चाहिन्छ")
    .regex(/[0-9]/, "कम्तीमा एउटा अंक चाहिन्छ")
    .regex(/[^A-Za-z0-9]/, "कम्तीमा एउटा विशेष चिन्ह चाहिन्छ"),
});

export const loginSchema = z.object({
  email: z.string().email("मान्य इमेल ठेगाना प्रविष्ट गर्नुहोस्"),
  password: z.string().min(1, "पासवर्ड आवश्यक छ"),
});

export const articleSchema = z.object({
  title: z.string().min(1, "शीर्षक आवश्यक छ"),
  title_en: z.string().optional(),
  slug: z.string().min(1, "स्लग आवश्यक छ"),
  excerpt: z.string().optional(),
  excerpt_en: z.string().optional(),
  content: z.string().min(1, "सामग्री आवश्यक छ"),
  content_en: z.string().optional(),
  category_id: z.string().min(1, "वर्ग आवश्यक छ"),
  featured_image: z.string().optional(),
  ai_summary: z.string().optional(),
  status: z.enum(["DRAFT", "PUBLISHED", "ARCHIVED"]).default("DRAFT"),
  is_featured: z.boolean().default(false),
  tag_ids: z.array(z.string()).optional(),
});

export const categorySchema = z.object({
  name: z.string().min(1, "नाम आवश्यक छ"),
  name_en: z.string().optional(),
  slug: z.string().min(1, "स्लग आवश्यक छ"),
  description: z.string().optional(),
  color: z.string().default("#c62828"),
  sort_order: z.number().default(0),
  parent_id: z.string().nullable().optional(),
});

export const commentSchema = z.object({
  content: z.string().min(1, "टिप्पणी खाली हुन सक्दैन").max(2000),
  article_id: z.string().min(1),
  parent_id: z.string().nullable().optional(),
});

export const passwordResetSchema = z.object({
  password: z
    .string()
    .min(8, "पासवर्ड कम्तीमा ८ अक्षरको हुनुपर्छ")
    .regex(/[A-Z]/, "कम्तीमा एउटा ठूलो अक्षर चाहिन्छ")
    .regex(/[a-z]/, "कम्तीमा एउटा सानो अक्षर चाहिन्छ")
    .regex(/[0-9]/, "कम्तीमा एउटा अंक चाहिन्छ")
    .regex(/[^A-Za-z0-9]/, "कम्तीमा एउटा विशेष चिन्ह चाहिन्छ"),
  token: z.string().min(1),
});
