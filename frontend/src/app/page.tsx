import Image from "next/image";
import Link from "next/link";
import { ActivityCard, RecruitmentCard, TestimonialCard } from "@/components/cards";
import { EmptyState, Heart, Marquee, Moon, SectionTitle, Sparkle, Squiggle, Star } from "@/components/decor";
import { getActivities, getModules, getRecruitments, getSite, getTestimonials } from "@/lib/api";

// Dipakai bila program unggulan belum diisi di admin.
const PILLARS = [
  { code: "Calistung", name: null, description: "Membaca, menulis, dan berhitung sebagai bekal dasar." },
  { code: "Logika & Matematika", name: null, description: "Melatih nalar dan cara memecahkan masalah lewat permainan." },
  { code: "Game Coding", name: null, description: "Kenalan dengan dasar pemrograman lewat game edukatif." },
];

const PROGRAM_LOOK: Record<string, { icon: string; href: string }> = {
  QULEARN: { icon: "📚", href: "/modul" },
  QRAFT: { icon: "🔧", href: "/modul#sains_teknologi" },
  QIDS: { icon: "🎉", href: "/dokumentasi" },
};
const PROGRAM_TONES = ["bg-coral-soft", "bg-tangerine-soft", "bg-peri-soft", "bg-lilac-soft"];

export default async function HomePage() {
  const [site, recruitments, activities, testimonials, modules] = await Promise.all([
    getSite(),
    getRecruitments(),
    getActivities(),
    getTestimonials(),
    getModules(),
  ]);
  const open = recruitments.filter((r) => r.is_open).slice(0, 3);
  const programs = site.programs?.length ? site.programs : PILLARS;
  // angka dampak dari admin; kalau kosong, hitung dari data yang ada
  const impact = site.impact?.length
    ? site.impact
    : [
        { value: `${site.stats.activities}+`, label: "kegiatan terdokumentasi" },
        { value: String(modules.length), label: "modul untuk tingkat Kecil, Sedang & Besar" },
      ];

  return (
    <>
      {/* HERO */}
      <section className="night-sky relative overflow-hidden">
        <Moon className="absolute -right-24 -top-24 h-72 w-72 opacity-95" />
        <Sparkle className="absolute left-[4%] top-16 h-5 w-5 animate-twinkle" color="white" />
        <Sparkle className="absolute left-[46%] top-10 h-4 w-4 animate-twinkle [animation-delay:.8s]" color="var(--color-sun)" />
        <div className="relative mx-auto grid max-w-6xl items-center gap-10 px-4 pb-24 pt-14 sm:px-6 md:grid-cols-[1.15fr_1fr] md:pt-24">
          <div className="relative">
            <p className="eyebrow mb-5">{site.tagline ?? "Sebut Namamu, Gapai Cita-Citamu!"}</p>
            <h1 className="font-display text-5xl leading-[1] sm:text-7xl">
              {site.hero_title ?? "Belajar bareng, tumbuh bareng."}
            </h1>
            <Squiggle className="mt-5 h-4 w-40" />
            <p className="mt-5 max-w-xl text-lg text-white/80">{site.hero_subtitle}</p>
            <div className="mt-8 flex flex-wrap gap-3">
              <Link href="/volunteer" className="btn btn-sun text-lg">
                Daftar jadi volunteer
              </Link>
              <Link href="/modul" className="btn btn-light text-lg">
                Lihat modul belajar
              </Link>
            </div>
          </div>

          {/* kolase kartu */}
          <div className="relative mx-auto h-[380px] w-full max-w-md sm:h-[440px]">
            <div className="absolute left-1/2 top-1/2 h-36 w-36 -translate-x-1/2 -translate-y-1/2 rounded-full bg-white p-3 shadow-[0_0_60px_10px_rgb(255_211_92/0.35)]">
              <Image src="/logo-namaqu.png" alt="Logo NamaQu" width={120} height={120} priority />
            </div>
            <div className="card absolute left-0 top-4 w-52 -rotate-6 bg-coral p-5 text-ink sm:w-60">
              <p className="font-hand text-2xl leading-none text-grape-dark">jadwal rutin</p>
              <p className="mt-2 font-display text-2xl font-semibold">{site.schedule_text ?? "Belajar bareng di basecamp"}</p>
            </div>
            {impact[0] && (
              <div className="card absolute right-0 top-0 w-44 rotate-6 bg-tangerine p-5 text-ink sm:w-52">
                <p className="font-display text-5xl">{impact[0].value}</p>
                <p className="font-semibold">{impact[0].label}</p>
              </div>
            )}
            {impact[1] && (
              <div className="card absolute bottom-10 left-0 w-56 rotate-3 bg-peri p-5 text-ink sm:w-64">
                <p className="font-display text-5xl">{impact[1].value}</p>
                <p className="font-semibold">{impact[1].label}</p>
              </div>
            )}
            <div className="card absolute bottom-0 right-0 -rotate-3 bg-sun px-4 py-3 text-ink">
              <p className="font-hand text-xl">📍 {site.basecamp_address ?? "Keputih, Surabaya"}</p>
            </div>
            <Star className="absolute right-6 top-1/2 h-10 w-10 animate-wiggle" color="var(--color-sun)" />
            <Heart className="absolute -bottom-4 left-1/3 h-9 w-9 -rotate-12" color="var(--color-coral)" />
          </div>
        </div>
      </section>

      <Marquee
        items={[
          ...programs.map((p) => (p.name ? `${p.code} · ${p.name}` : p.code)),
          "Calistung",
          "Sains & Teknologi",
          "Basecamp Keputih",
          "Belajar sambil main",
        ]}
      />

      {/* OPEN RECRUITMENT */}
      <section className="mx-auto max-w-6xl px-4 py-20 sm:px-6">
        <div className="flex flex-wrap items-end justify-between gap-4">
          <SectionTitle eyebrow="open recruitment" title="Yuk, jadi kakak volunteer!">
            Volunteer mingguan dibuka tiap pekan untuk kegiatan akhir pekan. Untuk kegiatan besar seperti QIDS (Hari
            Anak Nasional), pendaftaran dibuka lewat formulir.
          </SectionTitle>
          <Link href="/volunteer" className="btn btn-light mb-8">
            Semua info volunteer
          </Link>
        </div>
        {open.length ? (
          <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            {open.map((r) => (
              <RecruitmentCard key={r.id} r={r} />
            ))}
          </div>
        ) : (
          <EmptyState>
            Belum ada pendaftaran yang dibuka. Pantau Instagram{" "}
            <a className="font-semibold text-grape" href={`https://instagram.com/${site.instagram ?? "namaqu.id"}`}>
              @{site.instagram ?? "namaqu.id"}
            </a>{" "}
            untuk info terbaru!
          </EmptyState>
        )}
      </section>

      {/* MATERI */}
      <section className="scallop-bottom border-y-2 border-ink bg-lilac-soft pb-24 pt-20">
        <div className="mx-auto max-w-6xl px-4 sm:px-6">
          <SectionTitle eyebrow="program unggulan" title="Belajar, berkarya, merayakan" align="center">
            Materi disesuaikan dengan kemampuan tiap anak dalam tiga kelompok belajar: Kecil, Sedang, dan Besar. Orang
            tua bisa melihat modulnya di sini.
          </SectionTitle>
          <div className="grid gap-6 md:grid-cols-3">
            {programs.map((p, i) => {
              const look = PROGRAM_LOOK[p.code.toUpperCase()] ?? { icon: "⭐", href: "/modul" };
              return (
                <Link
                  key={p.code}
                  href={look.href}
                  className={`card group p-7 transition-transform hover:-translate-y-1 ${PROGRAM_TONES[i % PROGRAM_TONES.length]} ${i === 1 ? "md:translate-y-6" : ""}`}
                >
                  <span className="text-5xl">{look.icon}</span>
                  <h3 className="mt-4 font-display text-3xl group-hover:text-grape">{p.code}</h3>
                  {p.name && <p className="text-sm font-semibold uppercase tracking-wider text-grape">{p.name}</p>}
                  {p.description && <p className="mt-3 text-ink-soft">{p.description}</p>}
                </Link>
              );
            })}
          </div>
        </div>
      </section>

      {/* DOKUMENTASI */}
      <section className="mx-auto max-w-6xl px-4 py-20 sm:px-6">
        <div className="flex flex-wrap items-end justify-between gap-4">
          <SectionTitle eyebrow="jejak kegiatan" title="Cerita dari basecamp" />
          <Link href="/dokumentasi" className="btn btn-light mb-8">
            Lihat semua dokumentasi
          </Link>
        </div>
        {activities.length ? (
          <div className="grid grid-cols-2 gap-6 lg:grid-cols-4">
            {activities.slice(0, 4).map((a, i) => (
              <ActivityCard key={a.id} a={a} i={i} />
            ))}
          </div>
        ) : (
          <EmptyState>Dokumentasi kegiatan akan segera hadir.</EmptyState>
        )}
      </section>

      {/* TESTIMONI */}
      {testimonials.length > 0 && (
        <section className="mx-auto max-w-6xl px-4 pb-20 sm:px-6">
          <SectionTitle eyebrow="kesan & pesan" title="Kata kakak-kakak volunteer" />
          <div className="grid gap-6 md:grid-cols-3">
            {testimonials.slice(0, 3).map((t, i) => (
              <TestimonialCard key={t.id} t={t} i={i} />
            ))}
          </div>
          <Link href="/kesan-pesan" className="mt-8 inline-block font-semibold text-grape underline underline-offset-4">
            Baca kesan & pesan lainnya →
          </Link>
        </section>
      )}

      {/* KOLABORASI */}
      <section className="mx-auto max-w-6xl px-4 sm:px-6">
        <div className="card night-sky night-sky-violet relative overflow-hidden p-8 sm:p-12">
          <Sparkle className="absolute right-10 top-8 h-10 w-10" color="var(--color-sun)" />
          <Sparkle className="absolute bottom-8 right-32 h-6 w-6" color="var(--color-coral)" />
          <p className="font-hand text-3xl text-sun">untuk instansi & komunitas</p>
          <h2 className="mt-1 max-w-2xl font-display text-3xl font-semibold leading-tight sm:text-5xl">
            Mau berkolaborasi dengan NamaQu?
          </h2>
          <p className="mt-4 max-w-xl text-white/85">{site.collab_intro}</p>
          <Link href="/kolaborasi" className="btn btn-light mt-8">
            Lihat ketentuan kerja sama
          </Link>
        </div>
      </section>
    </>
  );
}
