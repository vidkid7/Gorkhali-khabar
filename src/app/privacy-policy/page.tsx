import type { Metadata } from "next";
import Link from "next/link";
import { Header } from "@/components/layout/Header";
import { Footer } from "@/components/layout/Footer";

export const metadata: Metadata = {
  title: "गोपनीयता नीति | Privacy Policy",
  description:
    "समाचार पोर्टलको गोपनीयता नीति - News Portal Privacy Policy",
};

export default function PrivacyPolicyPage() {
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
            <li className="font-medium text-foreground">गोपनीयता नीति</li>
          </ol>
        </nav>

        <article className="prose max-w-none">
          {/* Nepali */}
          <section className="mb-12">
            <h1 className="text-3xl font-bold mb-6">गोपनीयता नीति</h1>
            <p className="text-sm text-muted mb-4">
              अन्तिम अपडेट: {new Date().getFullYear()}-०१-०१
            </p>

            <h2 className="text-xl font-semibold mt-6 mb-3">१. परिचय</h2>
            <p className="text-muted leading-relaxed">
              समाचार पोर्टलमा स्वागत छ। हामी तपाईंको गोपनीयताको सम्मान गर्छौं र
              तपाईंको व्यक्तिगत जानकारीको सुरक्षा प्रति प्रतिबद्ध छौं। यो
              गोपनीयता नीतिले हामीले कसरी तपाईंको जानकारी सङ्कलन, प्रयोग र
              संरक्षण गर्छौं भनेर वर्णन गर्छ।
            </p>

            <h2 className="text-xl font-semibold mt-6 mb-3">
              २. सङ्कलन गरिने जानकारी
            </h2>
            <ul className="list-disc pl-6 text-muted space-y-1">
              <li>खाता दर्ताको लागि: नाम, इमेल ठेगाना</li>
              <li>
                स्वचालित रूपमा: IP ठेगाना, ब्राउजर प्रकार, उपकरण जानकारी
              </li>
              <li>कुकीहरू र समान प्रविधिहरू मार्फत प्रयोग डाटा</li>
            </ul>

            <h2 className="text-xl font-semibold mt-6 mb-3">
              ३. जानकारीको प्रयोग
            </h2>
            <p className="text-muted leading-relaxed">
              हामी तपाईंको जानकारी सेवा प्रदान, सुधार, र व्यक्तिगतकरणका लागि
              प्रयोग गर्छौं। हामी तपाईंको जानकारी तेस्रो पक्षसँग बिक्री
              गर्दैनौं।
            </p>

            <h2 className="text-xl font-semibold mt-6 mb-3">
              ४. डाटा सुरक्षा
            </h2>
            <p className="text-muted leading-relaxed">
              हामी तपाईंको डाटाको सुरक्षाका लागि उद्योग-मानक सुरक्षा उपायहरू
              प्रयोग गर्छौं।
            </p>

            <h2 className="text-xl font-semibold mt-6 mb-3">
              ५. सम्पर्क
            </h2>
            <p className="text-muted">
              गोपनीयता सम्बन्धी प्रश्नका लागि: privacy@newsportal.com
            </p>
          </section>

          {/* English */}
          <section className="border-t border-border pt-8">
            <h1 className="text-3xl font-bold mb-6">Privacy Policy</h1>
            <p className="text-sm text-muted mb-4">
              Last updated: {new Date().getFullYear()}-01-01
            </p>

            <h2 className="text-xl font-semibold mt-6 mb-3">
              1. Introduction
            </h2>
            <p className="text-muted leading-relaxed">
              Welcome to News Portal. We respect your privacy and are committed
              to protecting your personal information. This privacy policy
              describes how we collect, use, and safeguard your information.
            </p>

            <h2 className="text-xl font-semibold mt-6 mb-3">
              2. Information We Collect
            </h2>
            <ul className="list-disc pl-6 text-muted space-y-1">
              <li>For account registration: name, email address</li>
              <li>
                Automatically: IP address, browser type, device information
              </li>
              <li>Usage data through cookies and similar technologies</li>
            </ul>

            <h2 className="text-xl font-semibold mt-6 mb-3">
              3. Use of Information
            </h2>
            <p className="text-muted leading-relaxed">
              We use your information to provide, improve, and personalize our
              services. We do not sell your information to third parties.
            </p>

            <h2 className="text-xl font-semibold mt-6 mb-3">
              4. Data Security
            </h2>
            <p className="text-muted leading-relaxed">
              We implement industry-standard security measures to protect your
              data.
            </p>

            <h2 className="text-xl font-semibold mt-6 mb-3">5. Contact</h2>
            <p className="text-muted">
              For privacy inquiries: privacy@newsportal.com
            </p>
          </section>
        </article>
      </main>
      <Footer />
    </>
  );
}
