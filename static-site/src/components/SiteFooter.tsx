export function SiteFooter() {
  return (
    <footer className="site-footer">
      <div className="container footer-grid">
        <div>
          <img src="/icons/logo.svg" alt="Gorkhali Khabar" />
          <p>तथ्य, सन्दर्भ र जनसरोकारसहितको नेपाली पत्रकारिता।</p>
        </div>
        <nav aria-label="नीति लिंक">
          <a href="/about">हाम्रो बारेमा</a>
          <a href="/privacy-policy">गोपनीयता</a>
          <a href="/terms-of-service">प्रयोगका सर्त</a>
          <a href="/cookie-policy">कुकी नीति</a>
        </nav>
      </div>
      <div className="container copyright">
        © {new Date().getFullYear()} गोर्खाली खबर
      </div>
    </footer>
  );
}
