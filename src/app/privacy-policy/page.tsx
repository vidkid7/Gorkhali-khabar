import type { Metadata } from "next";
import { Header } from "@/components/layout/Header";
import { Footer } from "@/components/layout/Footer";
import { PublicPageHeader } from "@/components/ui/PublicPageHeader";
import { getSiteConfig } from "@/lib/site-config";

export const dynamic = "force-dynamic";

export const metadata: Metadata = {
  title: "गोपनीयता नीति | Privacy Policy",
  description:
    "Gorkhali Khabarको गोपनीयता नीति - Privacy Policy",
};

export default async function PrivacyPolicyPage() {
  const config = await getSiteConfig();
  const siteNameNe = config.site_name.ne;
  const siteNameEn = config.site_name.en;

  return (
    <>
      <Header />
      <main className="public-page-shell mx-auto max-w-4xl px-4 py-8">
        <PublicPageHeader title="गोपनीयता नीति" eyebrow="Privacy" description={`${siteNameNe} ले व्यक्तिगत जानकारी कसरी सङ्कलन, प्रयोग र सुरक्षित गर्छ।`} breadcrumbs={[{ label: "गृहपृष्ठ", href: "/" }, { label: "गोपनीयता नीति" }]} />

        <article className="document-prose prose max-w-none">
          {/* Nepali */}
          <section className="mb-12">
            <h2 className="sr-only">गोपनीयता नीति</h2>
            <p className="text-sm text-muted mb-4">
              अन्तिम अपडेट: {new Date().getFullYear()}-०१-०१
            </p>

            <h2 className="text-xl font-semibold mt-6 mb-3" style={{ fontFamily: "var(--font-nepali-serif)" }}>१. परिचय</h2>
            <p className="text-muted leading-relaxed">
              {siteNameNe}मा स्वागत छ। हामी तपाईंको गोपनीयताको सम्मान गर्छौं र
              तपाईंको व्यक्तिगत जानकारीको सुरक्षा प्रति प्रतिबद्ध छौं। यो
              गोपनीयता नीतिले हामीले कसरी तपाईंको जानकारी सङ्कलन, प्रयोग र
              संरक्षण गर्छौं भनेर वर्णन गर्छ।
            </p>

            <h2 className="text-xl font-semibold mt-6 mb-3" style={{ fontFamily: "var(--font-nepali-serif)" }}>
              २. सङ्कलन गरिने जानकारी
            </h2>
            <ul className="list-disc pl-6 text-muted space-y-1">
              <li>खाता दर्ताको लागि: नाम, इमेल ठेगाना</li>
              <li>
                स्वचालित रूपमा: IP ठेगाना, ब्राउजर प्रकार, उपकरण जानकारी
              </li>
              <li>कुकीहरू र समान प्रविधिहरू मार्फत प्रयोग डाटा</li>
            </ul>

            <h2 className="text-xl font-semibold mt-6 mb-3" style={{ fontFamily: "var(--font-nepali-serif)" }}>
              ३. जानकारीको प्रयोग
            </h2>
            <p className="text-muted leading-relaxed">
              हामी तपाईंको जानकारी सेवा प्रदान, सुधार, र व्यक्तिगतकरणका लागि
              प्रयोग गर्छौं। हामी तपाईंको जानकारी तेस्रो पक्षसँग बिक्री
              गर्दैनौं।
            </p>

            <h2 className="text-xl font-semibold mt-6 mb-3" style={{ fontFamily: "var(--font-nepali-serif)" }}>
              ४. डाटा सुरक्षा
            </h2>
            <p className="text-muted leading-relaxed">
              हामी तपाईंको डाटाको सुरक्षाका लागि उद्योग-मानक सुरक्षा उपायहरू
              प्रयोग गर्छौं।
            </p>

            <h2 className="text-xl font-semibold mt-6 mb-3" style={{ fontFamily: "var(--font-nepali-serif)" }}>
              ५. सम्पर्क
            </h2>
            {config.contact_email && (
              <p className="text-muted">गोपनीयता सम्बन्धी प्रश्नका लागि: {config.contact_email}</p>
            )}
          </section>

          {/* English */}
          <section className="border-t border-border pt-8">
            <h2 className="text-3xl font-bold mb-6" style={{ fontFamily: "var(--font-nepali-serif)" }}>Privacy Policy</h2>
            <p className="text-sm text-muted mb-4">
              Last updated: {new Date().getFullYear()}-01-01
            </p>

            <h2 className="text-xl font-semibold mt-6 mb-3" style={{ fontFamily: "var(--font-nepali-serif)" }}>
              1. Introduction
            </h2>
            <p className="text-muted leading-relaxed">
              Welcome to {siteNameEn}. We respect your privacy and are committed
              to protecting your personal information. This privacy policy
              describes how we collect, use, and safeguard your information.
            </p>

            <h2 className="text-xl font-semibold mt-6 mb-3" style={{ fontFamily: "var(--font-nepali-serif)" }}>
              2. Information We Collect
            </h2>
            <ul className="list-disc pl-6 text-muted space-y-1">
              <li>For account registration: name, email address</li>
              <li>
                Automatically: IP address, browser type, device information
              </li>
              <li>Usage data through cookies and similar technologies</li>
            </ul>

            <h2 className="text-xl font-semibold mt-6 mb-3" style={{ fontFamily: "var(--font-nepali-serif)" }}>
              3. Use of Information
            </h2>
            <p className="text-muted leading-relaxed">
              We use your information to provide, improve, and personalize our
              services. We do not sell your information to third parties.
            </p>

            <h2 className="text-xl font-semibold mt-6 mb-3" style={{ fontFamily: "var(--font-nepali-serif)" }}>
              4. Data Security
            </h2>
            <p className="text-muted leading-relaxed">
              We implement industry-standard security measures to protect your
              data.
            </p>

            <h2 className="text-xl font-semibold mt-6 mb-3" style={{ fontFamily: "var(--font-nepali-serif)" }}>5. Contact</h2>
            {config.contact_email && (
              <p className="text-muted">For privacy inquiries: {config.contact_email}</p>
            )}
          </section>
        </article>
      </main>
      <Footer />
    </>
  );
}
