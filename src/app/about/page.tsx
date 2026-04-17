import type { Metadata } from "next";
import Link from "next/link";
import { Header } from "@/components/layout/Header";
import { Footer } from "@/components/layout/Footer";

export const metadata: Metadata = {
  title: "हाम्रो बारेमा | About Us",
  description:
    "समाचार पोर्टलको बारेमा - About News Portal",
};

export default function AboutPage() {
  return (
    <>
      <Header />
      <main className="mx-auto max-w-4xl px-4 py-8">
        {/* Breadcrumb */}
        <nav className="text-sm text-muted mb-6" aria-label="Breadcrumb">
          <ol className="flex items-center gap-1">
            <li>
              <Link href="/" className="hover:text-accent">
                गृहपृष्ठ
              </Link>
            </li>
            <li>/</li>
            <li className="font-medium text-foreground">हाम्रो बारेमा</li>
          </ol>
        </nav>

        <article className="prose max-w-none">
          {/* Nepali */}
          <section className="mb-12">
            <h1 className="text-3xl font-bold mb-6">हाम्रो बारेमा</h1>

            <h2 className="text-xl font-semibold mt-6 mb-3">हाम्रो उद्देश्य</h2>
            <p className="text-muted leading-relaxed">
              समाचार पोर्टल नेपालको एक विश्वसनीय अनलाइन समाचार सेवा हो। हामी
              निष्पक्ष, सत्य र समयमा समाचार प्रदान गर्न प्रतिबद्ध छौं।
            </p>

            <h2 className="text-xl font-semibold mt-6 mb-3">हाम्रो दृष्टिकोण</h2>
            <p className="text-muted leading-relaxed">
              हामी विश्वास गर्छौं कि सही जानकारीमा पहुँच प्रत्येक नागरिकको
              अधिकार हो। हामी समाचारलाई सरल, स्पष्ट र सबैका लागि पहुँचयोग्य
              बनाउने लक्ष्य राख्छौं।
            </p>

            <h2 className="text-xl font-semibold mt-6 mb-3">हाम्रो टोली</h2>
            <p className="text-muted leading-relaxed">
              हाम्रो टोलीमा अनुभवी पत्रकार, सम्पादक, र प्रविधि विज्ञहरू छन्
              जसले दैनिक रूपमा तपाईंसम्म समाचार पुर्‍याउने काम गर्छन्।
            </p>

            <h2 className="text-xl font-semibold mt-6 mb-3">सम्पर्क</h2>
            <ul className="list-none pl-0 text-muted space-y-1">
              <li>📍 काठमाडौं, नेपाल</li>
              <li>📞 +977-1-4XXXXXX</li>
              <li>✉️ info@newsportal.com</li>
            </ul>
          </section>

          {/* English */}
          <section className="border-t border-border pt-8">
            <h1 className="text-3xl font-bold mb-6">About Us</h1>

            <h2 className="text-xl font-semibold mt-6 mb-3">Our Mission</h2>
            <p className="text-muted leading-relaxed">
              News Portal is a trusted online news service in Nepal. We are
              committed to delivering unbiased, truthful, and timely news.
            </p>

            <h2 className="text-xl font-semibold mt-6 mb-3">Our Vision</h2>
            <p className="text-muted leading-relaxed">
              We believe access to accurate information is the right of every
              citizen. Our goal is to make news simple, clear, and accessible
              to all.
            </p>

            <h2 className="text-xl font-semibold mt-6 mb-3">Our Team</h2>
            <p className="text-muted leading-relaxed">
              Our team consists of experienced journalists, editors, and
              technology experts who work daily to bring you the news.
            </p>

            <h2 className="text-xl font-semibold mt-6 mb-3">Contact</h2>
            <ul className="list-none pl-0 text-muted space-y-1">
              <li>📍 Kathmandu, Nepal</li>
              <li>📞 +977-1-4XXXXXX</li>
              <li>✉️ info@newsportal.com</li>
            </ul>
          </section>
        </article>
      </main>
      <Footer />
    </>
  );
}
