import type { Metadata } from "next";
import { TeamCard } from "@/components/cards";
import { EmptyState, PageHero, RichText, SectionTitle, Star } from "@/components/decor";
import { getSite, getTeam } from "@/lib/api";

export const metadata: Metadata = {
  title: "Tentang Kami & Pengurus",
  description: "Kenalan dengan NamaQu: filosofi nama, visi & misi, program unggulan, dan orang-orang di baliknya.",
};

const PROGRAM_ICON: Record<string, string> = { QULEARN: "📚", QRAFT: "🔧", QIDS: "🎉" };
const TONES = ["bg-coral-soft", "bg-tangerine-soft", "bg-peri-soft", "bg-lilac-soft"];

export default async function AboutPage() {
  const [site, team] = await Promise.all([getSite(), getTeam()]);
  const facts = [
    ["Basecamp", site.basecamp_address ?? "Keputih, Surabaya"],
    ["Kegiatan rutin", site.schedule_text ?? "Setiap akhir pekan"],
    ["Bidang", "Sosial & Pendidikan"],
    ["Untuk siapa", "Anak-anak usia 6–12 tahun di wilayah marginal"],
  ];
  const missions = site.missions ?? [];
  const programs = site.programs ?? [];
  const impact = site.impact ?? [];
  const milestones = site.milestones ?? [];

  return (
    <>
      <PageHero eyebrow="kenalan dulu, yuk" title={<>Tentang <span className="text-sun">NamaQu</span></>} tone="lilac">
        {site.tagline}
      </PageHero>

      {/* angka dampak */}
      {impact.length > 0 && (
        <section className="mx-auto -mt-6 max-w-6xl px-4 sm:px-6">
          <dl className="grid grid-cols-2 gap-4 md:grid-cols-4">
            {impact.map((s, i) => (
              <div key={s.label} className={`card p-5 text-center ${TONES[i % TONES.length]}`}>
                <dt className="sr-only">{s.label}</dt>
                <dd className="font-display text-4xl sm:text-5xl">{s.value}</dd>
                <p className="mt-1 text-sm font-semibold text-ink-soft">{s.label}</p>
              </div>
            ))}
          </dl>
        </section>
      )}

      <section className="mx-auto grid max-w-6xl gap-10 px-4 py-14 sm:px-6 lg:grid-cols-[1.4fr_1fr]">
        <RichText html={site.about} className="text-lg" />
        <dl className="card h-fit divide-y-2 divide-dashed divide-ink/20 bg-sun/40 p-6">
          {facts.map(([k, v]) => (
            <div key={k} className="py-3 first:pt-0 last:pb-0">
              <dt className="text-sm font-semibold text-ink-soft">{k}</dt>
              <dd className="font-display text-lg">{v}</dd>
            </div>
          ))}
        </dl>
      </section>

      {/* visi & misi */}
      {(site.vision || missions.length > 0) && (
        <section className="night-sky night-sky-violet scallop-bottom pb-20 pt-16">
          <div className="mx-auto grid max-w-6xl gap-10 px-4 sm:px-6 lg:grid-cols-[1fr_1.3fr]">
            {site.vision && (
              <div>
                <p className="eyebrow mb-4">visi</p>
                <p className="font-display text-3xl leading-snug sm:text-4xl">{site.vision}</p>
              </div>
            )}
            {missions.length > 0 && (
              <div>
                <p className="eyebrow mb-4">misi</p>
                <ol className="space-y-3">
                  {missions.map((m, i) => (
                    <li key={m} className="flex gap-3 rounded-2xl bg-white/10 p-4">
                      <span className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-sun font-display text-ink">
                        {i + 1}
                      </span>
                      <span className="text-white/90">{m}</span>
                    </li>
                  ))}
                </ol>
              </div>
            )}
          </div>
        </section>
      )}

      {/* program unggulan */}
      {programs.length > 0 && (
        <section id="program" className="mx-auto max-w-6xl scroll-mt-24 px-4 py-16 sm:px-6">
          <SectionTitle eyebrow="program unggulan" title="Tiga cara kami menemani adik-adik" />
          <div className="grid gap-6 md:grid-cols-3">
            {programs.map((p, i) => (
              <article key={p.code} className={`card p-7 ${TONES[i % TONES.length]}`}>
                <span className="text-5xl">{PROGRAM_ICON[p.code.toUpperCase()] ?? "⭐"}</span>
                <h3 className="mt-4 font-display text-3xl">{p.code}</h3>
                {p.name && <p className="text-sm font-semibold uppercase tracking-wider text-grape">{p.name}</p>}
                {p.description && <p className="mt-3 text-ink-soft">{p.description}</p>}
              </article>
            ))}
          </div>
        </section>
      )}

      {/* perjalanan */}
      {milestones.length > 0 && (
        <section className="mx-auto max-w-3xl px-4 pb-16 sm:px-6">
          <SectionTitle eyebrow="perjalanan kami" title="Dari pengabdian kampus menjadi komunitas" />
          <ol className="relative space-y-6 border-l-2 border-dashed border-grape/40 pl-8">
            {milestones.map((m) => (
              <li key={m.date + m.title} className="relative">
                <Star className="absolute -left-[45px] top-0.5 h-6 w-6" color="var(--color-sun)" />
                <p className="font-display text-xl text-grape">{m.date}</p>
                <p className="text-ink-soft">{m.title}</p>
              </li>
            ))}
          </ol>
        </section>
      )}

      <section className="mx-auto max-w-6xl px-4 pb-8 sm:px-6">
        <SectionTitle eyebrow="biodata pengurus" title="Orang-orang di balik NamaQu">
          Para penggagas yang menyiapkan kegiatan, modul, dan relawan setiap akhir pekan.
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
