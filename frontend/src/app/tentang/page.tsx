import type { Metadata } from "next";
import { TeamCard } from "@/components/cards";
import { EmptyState, PageHero, RichText, SectionTitle } from "@/components/decor";
import { getSite, getTeam } from "@/lib/api";

export const metadata: Metadata = {
  title: "Tentang Kami & Pengurus",
  description: "Kenalan dengan NamaQu dan orang-orang yang mengelola komunitas ini.",
};

export default async function AboutPage() {
  const [site, team] = await Promise.all([getSite(), getTeam()]);
  const facts = [
    ["Basecamp", site.basecamp_address ?? "Keputih, Surabaya"],
    ["Bidang", "Sosial & Pendidikan"],
    ["Untuk siapa", "Anak-anak sekolah dasar (SD)"],
    ["Sistem", "Komunitas independen, terbuka untuk volunteer"],
  ];

  return (
    <>
      <PageHero eyebrow="kenalan dulu, yuk" title={<>Tentang <span className="text-sun">NamaQu</span></>} tone="lilac" />

      <section className="mx-auto grid max-w-6xl gap-10 px-4 py-14 sm:px-6 lg:grid-cols-[1.4fr_1fr]">
        <RichText html={site.about} className="text-lg" />
        <dl className="card h-fit divide-y-2 divide-dashed divide-ink/20 bg-sun/40 p-6">
          {facts.map(([k, v]) => (
            <div key={k} className="py-3 first:pt-0 last:pb-0">
              <dt className="text-sm font-semibold text-ink-soft">{k}</dt>
              <dd className="font-display text-lg font-semibold">{v}</dd>
            </div>
          ))}
        </dl>
      </section>

      <section className="mx-auto max-w-6xl px-4 pb-8 sm:px-6">
        <SectionTitle eyebrow="biodata pengurus" title="Orang-orang di balik NamaQu">
          Founder dan pengurus yang menyiapkan kegiatan, materi, dan volunteer setiap minggunya.
        </SectionTitle>
        {team.length ? (
          <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            {team.map((m, i) => (
              <TeamCard key={m.id} m={m} i={i} />
            ))}
          </div>
        ) : (
          <EmptyState>Profil pengurus akan segera ditambahkan.</EmptyState>
        )}
      </section>
    </>
  );
}
