import type { Metadata } from "next";
import { PageHero } from "@/components/decor";
import { getSite, igLink, tiktokLink, waLink } from "@/lib/api";

export const metadata: Metadata = {
  title: "Kontak & Media Sosial",
  description: "Hubungi NamaQu untuk informasi, pendaftaran volunteer, maupun kolaborasi.",
};

export default async function ContactPage() {
  const site = await getSite();
  const ig = igLink(site.instagram);

  const channels = [
    ig && { label: "Instagram", value: `@${site.instagram}`, href: ig, tone: "bg-coral", icon: "📸" },
    site.tiktok && { label: "TikTok", value: `@${site.tiktok}`, href: tiktokLink(site.tiktok)!, tone: "bg-lilac", icon: "🎬" },
    site.linkedin && { label: "LinkedIn", value: "NamaQu", href: site.linkedin, tone: "bg-peri", icon: "💼" },
    site.email && { label: "Email", value: site.email, href: `mailto:${site.email}`, tone: "bg-tangerine", icon: "✉️" },
    site.maps_url && { label: "Basecamp", value: site.basecamp_address ?? "Keputih", href: site.maps_url, tone: "bg-sun", icon: "📍" },
  ].filter(Boolean) as { label: string; value: string; href: string; tone: string; icon: string }[];

  return (
    <>
      <PageHero eyebrow="say hi!" title={<>Kontak & <span className="text-sun">media sosial</span></>} tone="mint">
        Untuk informasi kegiatan, pendaftaran volunteer, maupun kerja sama — hubungi kami lewat kanal berikut.
      </PageHero>

      <section className="mx-auto max-w-6xl px-4 py-14 sm:px-6">
        <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
          {channels.map((c) => (
            <a key={c.label} href={c.href} target="_blank" rel="noreferrer" className={`card p-6 transition-transform hover:-translate-y-1 ${c.tone}`}>
              <span className="text-4xl">{c.icon}</span>
              <p className="mt-3 text-sm font-semibold uppercase tracking-wider">{c.label}</p>
              <p className="font-display text-xl font-semibold break-words">{c.value}</p>
            </a>
          ))}
        </div>

        <h2 className="mt-16 font-display text-3xl font-semibold">Contact Person</h2>
        <div className="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          {(site.contacts ?? []).map((c) => (
            <div key={c.whatsapp} className="card flex flex-col p-6">
              <p className="font-display text-2xl font-semibold">{c.name}</p>
              {c.purpose && <p className="text-ink-soft">{c.purpose}</p>}
              <a href={waLink(c.whatsapp) ?? "#"} target="_blank" rel="noreferrer" className="btn btn-primary mt-5 self-start">
                WhatsApp {c.name}
              </a>
            </div>
          ))}
          {!(site.contacts ?? []).length && <p className="text-ink-soft">Contact person belum ditambahkan.</p>}
        </div>
      </section>
    </>
  );
}
