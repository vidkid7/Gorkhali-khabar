import { useState } from "react";

const nav = [
  ["समाचार", "/categories/samachar"],
  ["राजनीति", "/categories/rajniti"],
  ["अर्थ", "/finance"],
  ["समाज", "/categories/samaj"],
  ["खेलकुद", "/sports"],
  ["प्रविधि", "/categories/prabidhi"],
  ["रिल्स", "/reels"],
  ["फोटो", "/galleries"],
  ["पात्रो", "/patro"],
];

export function SiteHeader() {
  const [open, setOpen] = useState(false);
  return (
    <header className="site-header">
      <div className="top-strip">
        <div className="container top-strip__inner">
          <span>नेपालको विश्वसनीय अनलाइन समाचार पोर्टल</span>
          <a href="/gorkhali-admin">सम्पादकीय प्रवेश</a>
        </div>
      </div>
      <div className="container masthead">
        <a href="/" className="brand" aria-label="गोर्खाली खबर गृहपृष्ठ">
          <img src="/icons/logo.svg" alt="Gorkhali Khabar" />
        </a>
        <a className="search-link" href="/search" aria-label="समाचार खोज्नुहोस्">
          खोज्नुहोस्
        </a>
        <button
          className="menu-button"
          type="button"
          aria-expanded={open}
          aria-controls="public-navigation"
          onClick={() => setOpen((value) => !value)}
        >
          मेनु
        </button>
      </div>
      <nav
        id="public-navigation"
        className={`primary-nav ${open ? "is-open" : ""}`}
        aria-label="मुख्य नेभिगेसन"
      >
        <div className="container nav-links">
          {nav.map(([label, href]) => (
            <a key={href} href={href}>
              {label}
            </a>
          ))}
        </div>
      </nav>
    </header>
  );
}
