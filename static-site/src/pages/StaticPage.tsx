import { NotFoundPage } from "../components/PageState";

const pages: Record<string, { title: string; eyebrow: string; paragraphs: string[] }> = {
  about: {
    title: "हाम्रो बारेमा",
    eyebrow: "Gorkhali Khabar",
    paragraphs: [
      "गोर्खाली खबर तथ्य, सन्दर्भ र जनसरोकारलाई केन्द्रमा राख्ने नेपाली डिजिटल समाचार माध्यम हो।",
      "हामी निष्पक्षता, शुद्धता र सार्वजनिक उत्तरदायित्वलाई सम्पादकीय अभ्यासको आधार मान्छौँ।",
    ],
  },
  "privacy-policy": {
    title: "गोपनीयता नीति",
    eyebrow: "Privacy",
    paragraphs: [
      "हामी सेवा सञ्चालनका लागि आवश्यक न्यूनतम प्राविधिक विवरण मात्र प्रयोग गर्छौँ।",
      "व्यक्तिगत जानकारी कानुनले मागेको अवस्था बाहेक तेस्रो पक्षलाई बिक्री वा अनधिकृत रूपमा हस्तान्तरण गरिँदैन।",
    ],
  },
  "terms-of-service": {
    title: "प्रयोगका सर्त",
    eyebrow: "Terms",
    paragraphs: [
      "गोर्खाली खबरको सामग्री व्यक्तिगत र गैरव्यावसायिक जानकारीका लागि प्रयोग गर्न सकिन्छ।",
      "सामग्री पुनःप्रकाशन गर्दा पूर्वस्वीकृति, उचित श्रेय र मूल लिंक आवश्यक हुन्छ।",
    ],
  },
  "cookie-policy": {
    title: "कुकी नीति",
    eyebrow: "Cookies",
    paragraphs: [
      "यो साइटले आधारभूत कार्यक्षमता, सुरक्षा र सेवा सुधारका लागि सीमित कुकी प्रयोग गर्न सक्छ।",
      "तपाईं आफ्नो ब्राउजर सेटिङबाट कुकी नियन्त्रण वा हटाउन सक्नुहुन्छ।",
    ],
  },
};

export function StaticPage({ slug }: { slug: string }) {
  const page = pages[slug];
  if (!page) return <NotFoundPage />;
  return (
    <main className="static-page container">
      <p className="eyebrow">{page.eyebrow}</p>
      <h1>{page.title}</h1>
      {page.paragraphs.map((paragraph) => <p key={paragraph}>{paragraph}</p>)}
    </main>
  );
}
