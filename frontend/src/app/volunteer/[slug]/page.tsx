import type { Metadata } from "next";
import Image from "next/image";
import Link from "next/link";
import { notFound } from "next/navigation";
import { RichText, Star } from "@/components/decor";
import { formatDate, getRecruitment, getRecruitments, getSite, igLink } from "@/lib/api";
import { RegisterForm } from "./register-form";

// Halaman detail sudah di-prerender; slug yang belum dikenal boleh menunggu render agar status 404 tetap benar.
export const instant = false;

export async function generateStaticParams() {
  const items = await getRecruitments();
  return items.length ? items.map((r) => ({ slug: r.slug })) : [{ slug: "__placeholder__" }];
}

export async function generateMetadata({ params }: PageProps<"/volunteer/[slug]">): Promise<Metadata> {
  const r = await getRecruitment((await params).slug);
  return r ? { title: r.title, description: r.summary ?? undefined } : {};
}

export default async function RecruitmentPage({ params }: PageProps<"/volunteer/[slug]">) {
  const { slug } = await params;
  const [r, site] = await Promise.all([getRecruitment(slug), getSite()]);
  if (!r) notFound();

  const ig = igLink(site.instagram);
  const info = [
    ["Tanggal", r.activity_date ? formatDate(r.activity_date, { weekday: "long" }) : "Menyusul"],
    ["Jam", r.activity_time],
    ["Lokasi", r.location],
    ["Pendaftaran dibuka", formatDate(r.registration_opens_at)],
    ["Batas pendaftaran", formatDate(r.registration_closes_at)],
    ["Kuota", r.quota ? `${r.quota} volunteer` : null],
  ].filter(([, v]) => v) as [string, string][];

  return (
    <div className="mx-auto max-w-6xl px-4 py-12 sm:px-6">
      <Link href="/volunteer" className="font-semibold text-ink-soft hover:text-grape">
        ← Semua open recruitment
      </Link>

      <div className="mt-6 grid gap-10 lg:grid-cols-[1.2fr_1fr]">
        <div>
          <span className={`chip ${r.type === "event" ? "bg-grape text-white" : "bg-tangerine"}`}>{r.type_label}</span>
          <h1 className="mt-3 font-display text-4xl font-semibold leading-tight sm:text-5xl">{r.title}</h1>
          {r.summary && <p className="mt-3 text-lg text-ink-soft">{r.summary}</p>}

          <dl className="card mt-8 grid gap-x-6 gap-y-3 bg-sun/40 p-6 sm:grid-cols-2">
            {info.map(([k, v]) => (
              <div key={k}>
                <dt className="text-sm font-semibold text-ink-soft">{k}</dt>
                <dd className="font-semibold">{v}</dd>
              </div>
            ))}
          </dl>

          {r.requirements.length > 0 && (
            <div className="mt-10">
              <h2 className="font-display text-2xl font-semibold">Persyaratan</h2>
              <ul className="mt-4 space-y-3">
                {r.requirements.map((req) => (
                  <li key={req} className="flex items-start gap-3">
                    <Star className="mt-0.5 h-5 w-5 shrink-0" />
                    <span>{req}</span>
                  </li>
                ))}
              </ul>
            </div>
          )}

          {r.description && (
            <div className="mt-10">
              <h2 className="font-display text-2xl font-semibold">Detail & ketentuan</h2>
              <RichText html={r.description} className="mt-2" />
            </div>
          )}

          {r.poster && (
            <div className="relative mt-10 aspect-[4/5] max-w-md overflow-hidden rounded-3xl border-2 border-ink">
              <Image src={r.poster} alt={`Poster ${r.title}`} fill sizes="448px" className="object-cover" />
            </div>
          )}
        </div>

        <aside className="lg:sticky lg:top-24 lg:self-start">
          {!r.is_open ? (
            <div className="card bg-lilac-soft p-8 text-center">
              <p className="text-4xl">🌙</p>
              <h3 className="mt-2 font-display text-2xl font-semibold">Pendaftaran ditutup</h3>
              <p className="mt-2 text-ink-soft">
                Terima kasih atas antusiasmenya! Pantau Instagram NamaQu untuk open recruitment berikutnya.
              </p>
              {ig && (
                <a href={ig} target="_blank" rel="noreferrer" className="btn btn-pink mt-5">
                  @{site.instagram}
                </a>
              )}
            </div>
          ) : r.registration_method === "form" ? (
            <RegisterForm slug={r.slug} />
          ) : r.registration_method === "instagram" ? (
            <div className="card bg-coral-soft p-8">
              <h3 className="font-display text-2xl font-semibold">Daftar lewat Instagram Story</h3>
              <ol className="mt-4 list-decimal space-y-2 pl-5 text-ink-soft">
                <li>Follow & like postingan Instagram @{site.instagram ?? "namaqu.id"}.</li>
                <li>Buka Story open recruitment minggu ini.</li>
                <li>Balas Story sesuai format yang diminta.</li>
                <li>Tunggu konfirmasi dari pengurus lewat DM.</li>
              </ol>
              <a href={r.external_url ?? ig ?? "#"} target="_blank" rel="noreferrer" className="btn btn-primary mt-6 w-full">
                Buka Story di Instagram
              </a>
            </div>
          ) : (
            <div className="card bg-peri-soft p-8">
              <h3 className="font-display text-2xl font-semibold">Formulir pendaftaran</h3>
              <p className="mt-2 text-ink-soft">Pendaftaran kegiatan ini menggunakan formulir eksternal.</p>
              <a href={r.external_url ?? "#"} target="_blank" rel="noreferrer" className="btn btn-primary mt-6 w-full">
                Buka formulir
              </a>
            </div>
          )}
        </aside>
      </div>
    </div>
  );
}
