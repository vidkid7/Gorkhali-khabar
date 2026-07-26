import type { Metadata } from "next";
import { Check, Mail, MapPin, Phone } from "lucide-react";
import { Header } from "@/components/layout/Header";
import { Footer } from "@/components/layout/Footer";
import { getSiteConfig } from "@/lib/site-config";
import { canonicalUrl, defaultOpenGraphImage } from "@/lib/seo";
import { PublicPageHeader } from "@/components/ui/PublicPageHeader";

export const dynamic = "force-dynamic";

export const metadata: Metadata = {
  title: "हाम्रो बारेमा | About Us",
  description:
    "Gorkhali Khabarको बारेमा - Nepal's trusted online news portal delivering unbiased, timely news in Nepali and English.",
  alternates: { canonical: canonicalUrl("/about") },
  openGraph: {
    title: "हाम्रो बारेमा | About Us",
    description: "Gorkhali Khabarको बारेमा",
    url: canonicalUrl("/about"),
    images: [defaultOpenGraphImage()],
  },
  twitter: {
    card: "summary_large_image",
    title: "हाम्रो बारेमा | About Us",
    description: "Gorkhali Khabarको बारेमा",
    images: [defaultOpenGraphImage()],
  },
};

type AboutContent = {
  eyebrow: string;
  name: string;
  mission: string;
  trust: string[];
  valuesTitle: string;
  values: { number: string; title: string; copy: string }[];
  standardsTitle: string;
  standards: { title: string; copy: string }[];
  newsroomTitle: string;
  newsroomCopy: string;
  contactTitle: string;
  contactCopy: string;
  addressLabel: string;
  phoneLabel: string;
  emailLabel: string;
};

function EditorialAbout({
  content,
  address,
  phone,
  email,
}: {
  content: AboutContent;
  address?: string;
  phone?: string;
  email?: string;
}) {
  return (
    <article className="about-editorial">
      <section className="about-hero" aria-labelledby="about-title">
        <div className="about-hero__rule" aria-hidden="true" />
        <p className="about-hero__eyebrow">{content.eyebrow}</p>
        <h2 id="about-title">{content.name}</h2>
        <p className="about-hero__mission">{content.mission}</p>
        <ul className="about-trust-markers" aria-label="Editorial commitments">
          {content.trust.map((item) => (
            <li key={item}>
              <Check aria-hidden="true" size={15} strokeWidth={3} />
              {item}
            </li>
          ))}
        </ul>
      </section>

      <section className="about-section" data-testid="about-values" aria-labelledby="about-values-title">
        <div className="about-section__heading">
          <p>{content.eyebrow}</p>
          <h2 id="about-values-title">{content.valuesTitle}</h2>
        </div>
        <div className="about-values-grid">
          {content.values.map((value) => (
            <section className="about-value" key={value.number}>
              <span aria-hidden="true">{value.number}</span>
              <h3>{value.title}</h3>
              <p>{value.copy}</p>
            </section>
          ))}
        </div>
      </section>

      <section className="about-section about-standards" data-testid="about-standards" aria-labelledby="about-standards-title">
        <div className="about-section__heading">
          <p>{content.eyebrow}</p>
          <h2 id="about-standards-title">{content.standardsTitle}</h2>
        </div>
        <ol>
          {content.standards.map((standard, index) => (
            <li key={standard.title}>
              <span aria-hidden="true">{String(index + 1).padStart(2, "0")}</span>
              <div>
                <h3>{standard.title}</h3>
                <p>{standard.copy}</p>
              </div>
            </li>
          ))}
        </ol>
      </section>

      <section className="about-newsroom" aria-labelledby="about-newsroom-title">
        <p>{content.eyebrow}</p>
        <h2 id="about-newsroom-title">{content.newsroomTitle}</h2>
        <p>{content.newsroomCopy}</p>
      </section>

      <section className="about-contact" data-testid="about-contact" aria-labelledby="about-contact-title">
        <div>
          <p className="about-contact__eyebrow">{content.eyebrow}</p>
          <h2 id="about-contact-title">{content.contactTitle}</h2>
          <p>{content.contactCopy}</p>
        </div>
        <address>
          {address && <p><MapPin aria-hidden="true" size={18} /><span>{content.addressLabel}</span>{address}</p>}
          {phone && <p><Phone aria-hidden="true" size={18} /><span>{content.phoneLabel}</span><a href={`tel:${phone.replace(/\s/g, "")}`}>{phone}</a></p>}
          {email && <p><Mail aria-hidden="true" size={18} /><span>{content.emailLabel}</span><a href={`mailto:${email}`}>{email}</a></p>}
        </address>
      </section>
    </article>
  );
}

export default async function AboutPage() {
  const config = await getSiteConfig();

  const nepali: AboutContent = {
    eyebrow: "गोर्खाली खबर • सार्वजनिक हित",
    name: config.site_name.ne,
    mission: "सत्य, सन्तुलित र समयमै समाचारमार्फत सचेत नागरिक समाज निर्माणमा हाम्रो निरन्तर प्रयास।",
    trust: ["तथ्यमा आधारित", "सन्तुलित दृष्टिकोण", "समयमै अपडेट"],
    valuesTitle: "हामी किन छौं",
    values: [
      { number: "०१", title: "हाम्रो उद्देश्य", copy: "विश्वसनीय समाचारलाई सबैको पहुँचमा पुर्‍याउनु र सार्वजनिक सरोकारका विषयलाई स्पष्ट बनाउनु।" },
      { number: "०२", title: "हाम्रो दृष्टि", copy: "सूचनामा नागरिकको अधिकारलाई सम्मान गर्दै जिम्मेवार र समावेशी डिजिटल पत्रकारिता गर्नु।" },
      { number: "०३", title: "सार्वजनिक सेवा", copy: "समुदायका आवाज, प्रश्न र उपलब्धिलाई अर्थपूर्ण संवादमा रूपान्तरण गर्नु।" },
    ],
    standardsTitle: "सम्पादकीय मापदण्ड",
    standards: [
      { title: "प्रमाणीकरण", copy: "प्रकाशनअघि उपलब्ध तथ्य, स्रोत र सान्दर्भिक दाबी जाँच्ने हाम्रो प्राथमिकता हो।" },
      { title: "सन्तुलन", copy: "सम्भव भएसम्म सरोकारवाला दृष्टिकोणलाई निष्पक्ष र सम्मानजनक रूपमा समेट्छौं।" },
      { title: "सुधार", copy: "त्रुटि देखिएमा त्यसलाई पारदर्शी ढंगले सच्याउन हामी प्रतिबद्ध छौं।" },
      { title: "जवाफदेहिता", copy: "हाम्रा निर्णय, प्राथमिकता र प्रकाशित सामग्रीप्रति पाठक र सार्वजनिक हितमा उत्तरदायी रहन्छौं।" },
    ],
    newsroomTitle: "समाचारकक्षको प्रतिबद्धता",
    newsroomCopy: "हाम्रो समाचारकक्षले स्थानीय अनुभव, राष्ट्रिय प्राथमिकता र विश्वका घटनालाई पाठकका लागि उपयोगी सन्दर्भमा राख्ने प्रयास गर्छ। हामी पाठकको विश्वासलाई दैनिक जिम्मेवारीका रूपमा लिन्छौं।",
    contactTitle: "सम्पर्कमा रहनुहोस्",
    contactCopy: "समाचार सुझाव, प्रतिक्रिया वा संस्थागत जानकारीका लागि हाम्रो टोलीसँग सम्पर्क गर्नुहोस्।",
    addressLabel: "ठेगाना",
    phoneLabel: "फोन",
    emailLabel: "इमेल",
  };

  const english: AboutContent = {
    eyebrow: "Gorkhali Khabar • Public Service",
    name: config.site_name.en,
    mission: "Our continuing effort is to strengthen an informed civic society through accurate, balanced, and timely news.",
    trust: ["Fact-led reporting", "Balanced perspective", "Timely updates"],
    valuesTitle: "What guides us",
    values: [
      { number: "01", title: "Our purpose", copy: "To make trustworthy reporting accessible and clarify issues that matter to the public." },
      { number: "02", title: "Our vision", copy: "To practise responsible, inclusive digital journalism that respects every citizen's right to information." },
      { number: "03", title: "Public service", copy: "To turn the voices, questions, and achievements of communities into meaningful public dialogue." },
    ],
    standardsTitle: "Editorial standards",
    standards: [
      { title: "Verification", copy: "Before publication, we prioritise checking available facts, sources, and material claims." },
      { title: "Balance", copy: "Where possible, we include relevant perspectives fairly and with respect." },
      { title: "Corrections", copy: "When an error is identified, we are committed to correcting it transparently." },
      { title: "Accountability", copy: "We remain answerable to readers and the public interest for our editorial decisions, priorities, and published work." },
    ],
    newsroomTitle: "A newsroom with a public responsibility",
    newsroomCopy: "Our newsroom works to place local experience, national priorities, and world events in useful context for readers. We treat reader trust as a daily responsibility.",
    contactTitle: "Stay in touch",
    contactCopy: "Reach our team with news tips, feedback, or organisational enquiries.",
    addressLabel: "Address",
    phoneLabel: "Phone",
    emailLabel: "Email",
  };

  return (
    <>
      <Header />
      <main className="public-page-shell about-page-shell mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        <div className="language-ne">
          <PublicPageHeader title="हाम्रो बारेमा" eyebrow={config.site_name.en} description="विश्वसनीय, स्पष्ट र समयमै समाचारका लागि हाम्रो दृष्टिकोण।" breadcrumbs={[{ label: "गृहपृष्ठ", href: "/" }, { label: "हाम्रो बारेमा" }]} />
        </div>
        <div className="language-en">
          <PublicPageHeader title="About Us" eyebrow={config.site_name.en} description="Our approach to trustworthy, clear, and timely reporting." breadcrumbs={[{ label: "Home", href: "/" }, { label: "About Us" }]} />
        </div>
        <div className="language-ne"><EditorialAbout content={nepali} address={config.contact_address.ne} phone={config.contact_phone} email={config.contact_email} /></div>
        <div className="language-en"><EditorialAbout content={english} address={config.contact_address.en} phone={config.contact_phone} email={config.contact_email} /></div>
      </main>
      <Footer />
    </>
  );
}
