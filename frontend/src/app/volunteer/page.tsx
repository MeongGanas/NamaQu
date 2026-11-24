import type { Metadata } from "next";
import Link from "next/link";
import { RecruitmentCard } from "@/components/cards";
import { EmptyState, PageHero, SectionTitle } from "@/components/decor";
import { getRecruitments, getSite, igLink } from "@/lib/api";

export const metadata: Metadata = {
  title: "Open Recruitment Volunteer",
  description: "Jadwal, persyaratan, dan cara mendaftar volunteer mingguan maupun kegiatan besar NamaQu.",
};

export default async function VolunteerPage() {
  const [items, site] = await Promise.all([getRecruitments(), getSite()]);
  const weekly = items.filter((r) => r.type === "weekly");
  const events = items.filter((r) => r.type === "event");
  const ig = igLink(site.instagram);

  const steps = [
    { n: "1", title: "Follow & like", text: `Follow Instagram @${site.instagram ?? "namaqu.id"} dan like postingan open recruitment.` },
    { n: "2", title: "Balas Story", text: "Untuk volunteer mingguan, balas Story open recruitment sesuai format yang diminta." },
    { n: "3", title: "Isi formulir", text: "Untuk kegiatan besar, isi formulir pendaftaran di halaman kegiatannya sebelum batas waktu." },
    { n: "4", title: "Tunggu kabar", text: "Pengurus akan menghubungi volunteer terpilih lewat WhatsApp/DM." },
  ];

  return (
    <>
      <PageHero eyebrow="open recruitment" title={<>Jadi kakak volunteer <span className="text-sun">NamaQu</span></>}>
        Satu pagi di hari Minggu bisa berarti banyak buat adik-adik di Keputih. Cek jadwal, syarat, dan cara daftarnya di
        bawah ini.
      </PageHero>

      <section className="mx-auto max-w-6xl px-4 py-16 sm:px-6">
        <SectionTitle eyebrow="cara daftar" title="Gampang, kok!" />
        <ol className="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
          {steps.map((s, i) => (
            <li key={s.n} className={`card p-6 ${["bg-coral-soft", "bg-tangerine-soft", "bg-peri-soft", "bg-sun/50"][i]}`}>
              <span className="flex h-10 w-10 items-center justify-center rounded-full border-2 border-ink bg-white font-display text-xl font-bold">
                {s.n}
              </span>
              <h3 className="mt-3 font-display text-xl font-semibold">{s.title}</h3>
              <p className="mt-1 text-sm text-ink-soft">{s.text}</p>
            </li>
          ))}
        </ol>
        {ig && (
          <a href={ig} target="_blank" rel="noreferrer" className="btn btn-pink mt-8">
            Buka Instagram @{site.instagram}
          </a>
        )}
      </section>

      <section id="mingguan" className="mx-auto max-w-6xl scroll-mt-24 px-4 pb-16 sm:px-6">
        <SectionTitle eyebrow="rutin tiap pekan" title="Volunteer Mingguan">
          Untuk kegiatan belajar setiap hari Minggu di basecamp. Info dibuka lewat Instagram Story setiap minggu.
        </SectionTitle>
        {weekly.length ? (
          <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            {weekly.map((r) => (
              <RecruitmentCard key={r.id} r={r} />
            ))}
          </div>
        ) : (
          <EmptyState>Jadwal volunteer mingguan berikutnya akan diumumkan lewat Instagram Story.</EmptyState>
        )}
      </section>

      <section id="kegiatan-besar" className="mx-auto max-w-6xl scroll-mt-24 px-4 pb-8 sm:px-6">
        <SectionTitle eyebrow="event spesial" title="Volunteer Kegiatan Besar">
          Untuk perayaan Hari Anak dan event sosial lainnya. Periode pendaftaran diumumkan setiap bulan sesuai kalender
          kegiatan.
        </SectionTitle>
        {events.length ? (
          <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            {events.map((r) => (
              <RecruitmentCard key={r.id} r={r} />
            ))}
          </div>
        ) : (
          <EmptyState>Belum ada kegiatan besar yang membuka pendaftaran.</EmptyState>
        )}
        <p className="mt-10 text-ink-soft">
          Penasaran rasanya jadi volunteer?{" "}
          <Link href="/kesan-pesan" className="font-semibold text-grape underline underline-offset-4">
            Baca kesan & pesan mereka →
          </Link>
        </p>
      </section>
    </>
  );
}
