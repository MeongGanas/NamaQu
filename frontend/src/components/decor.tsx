/** Ornamen kecil ala stiker scrapbook. Murni SVG, tanpa JS. */

export function Star({ className = "", color = "var(--color-sun)" }: { className?: string; color?: string }) {
  return (
    <svg viewBox="0 0 40 40" aria-hidden className={className}>
      <path
        d="M20 2 L24.5 14.5 L38 15.2 L27.3 23.6 L31 37 L20 29.3 L9 37 L12.7 23.6 L2 15.2 L15.5 14.5 Z"
        fill={color}
        stroke="var(--color-ink)"
        strokeWidth="2"
        strokeLinejoin="round"
      />
    </svg>
  );
}

export function Sparkle({ className = "", color = "var(--color-grape)" }: { className?: string; color?: string }) {
  return (
    <svg viewBox="0 0 24 24" aria-hidden className={className}>
      <path d="M12 0 C13 8 16 11 24 12 C16 13 13 16 12 24 C11 16 8 13 0 12 C8 11 11 8 12 0 Z" fill={color} />
    </svg>
  );
}

export function Heart({ className = "", color = "var(--color-coral)" }: { className?: string; color?: string }) {
  return (
    <svg viewBox="0 0 32 30" aria-hidden className={className}>
      <path
        d="M16 28 C6 21 2 15 2 9.5 C2 5 5.5 2 9.5 2 C12.5 2 14.8 3.8 16 6 C17.2 3.8 19.5 2 22.5 2 C26.5 2 30 5 30 9.5 C30 15 26 21 16 28 Z"
        fill={color}
        stroke="var(--color-ink)"
        strokeWidth="2"
      />
    </svg>
  );
}

export function Squiggle({ className = "" }: { className?: string }) {
  return (
    <svg viewBox="0 0 120 14" aria-hidden className={className} fill="none">
      <path
        d="M2 8 C12 0 22 0 32 8 S52 16 62 8 S82 0 92 8 S112 16 118 8"
        stroke="var(--color-sun)"
        strokeWidth="3"
        strokeLinecap="round"
      />
    </svg>
  );
}

export function Marquee({ items, className = "" }: { items: string[]; className?: string }) {
  const row = [...items, ...items];
  return (
    <div className={`overflow-hidden border-y-2 border-ink bg-night-2 py-2.5 text-white ${className}`}>
      <div className="flex w-max animate-marquee gap-8 whitespace-nowrap font-display text-lg tracking-wide">
        {row.map((t, i) => (
          <span key={i} className="flex items-center gap-8">
            {t}
            <Sparkle className="h-4 w-4" color="var(--color-sun)" />
          </span>
        ))}
      </div>
    </div>
  );
}

export function SectionTitle({
  eyebrow,
  title,
  children,
  align = "left",
}: {
  eyebrow?: string;
  title: React.ReactNode;
  children?: React.ReactNode;
  align?: "left" | "center";
}) {
  return (
    <div className={`mb-8 max-w-2xl ${align === "center" ? "mx-auto text-center" : ""}`}>
      {eyebrow && <p className="eyebrow mb-1">{eyebrow}</p>}
      <h2 className="font-display text-3xl font-semibold leading-tight tracking-tight sm:text-4xl">{title}</h2>
      {children && <p className="mt-3 text-ink-soft">{children}</p>}
    </div>
  );
}

export function PageHero({
  eyebrow,
  title,
  children,
  tone = "pink",
}: {
  eyebrow: string;
  title: React.ReactNode;
  children?: React.ReactNode;
  tone?: "pink" | "mint" | "sky" | "lilac";
}) {
  // dua varian langit seperti di materi brand: biru malam & indigo-ungu
  const violet = tone === "mint" || tone === "lilac";
  return (
    <section className={`scallop-bottom night-sky relative overflow-hidden pb-20 pt-14 sm:pt-20 ${violet ? "night-sky-violet" : ""}`}>
      <Moon className="absolute -right-16 -top-16 h-56 w-56 sm:-right-10 sm:-top-10" />
      <Star className="absolute right-[22%] top-10 h-8 w-8 animate-twinkle" color="var(--color-sun)" />
      <Sparkle className="absolute left-[5%] top-10 h-5 w-5 animate-twinkle" color="white" />
      <Sparkle className="absolute bottom-12 left-[42%] h-4 w-4 animate-twinkle [animation-delay:1.2s]" color="var(--color-sun)" />
      <div className="relative mx-auto max-w-6xl px-4 sm:px-6">
        <p className="eyebrow mb-4">{eyebrow}</p>
        <h1 className="max-w-3xl font-display text-4xl leading-[1.05] sm:text-6xl">{title}</h1>
        {children && <div className="mt-4 max-w-2xl text-lg text-white/80">{children}</div>}
      </div>
    </section>
  );
}

/** Bulan kuning di pojok, motif dari halaman-halaman T&C NamaQu. */
export function Moon({ className = "" }: { className?: string }) {
  return (
    <svg viewBox="0 0 200 200" aria-hidden className={className}>
      <circle cx="100" cy="100" r="96" fill="var(--color-sun)" />
      <circle cx="100" cy="100" r="96" fill="none" stroke="white" strokeOpacity="0.25" strokeWidth="10" />
    </svg>
  );
}

export function EmptyState({ children }: { children: React.ReactNode }) {
  return (
    <div className="stitch rounded-3xl bg-white/70 px-6 py-12 text-center text-ink-soft">
      <Heart className="mx-auto mb-3 h-8 w-8" />
      {children}
    </div>
  );
}

export function RichText({ html, className = "" }: { html: string | null | undefined; className?: string }) {
  if (!html) return null;
  // HTML sudah disanitasi di backend (Str::sanitizeHtml).
  return <div className={`prose-namaqu ${className}`} dangerouslySetInnerHTML={{ __html: html }} />;
}
