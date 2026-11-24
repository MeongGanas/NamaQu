import type { Metadata } from "next";
import Image from "next/image";
import Link from "next/link";
import { notFound } from "next/navigation";
import { RichText } from "@/components/decor";
import { formatDate, getActivities, getActivity } from "@/lib/api";

// Halaman detail sudah di-prerender; slug yang belum dikenal boleh menunggu render agar status 404 tetap benar.
export const instant = false;

export async function generateStaticParams() {
  const items = await getActivities();
  return items.length ? items.map((a) => ({ slug: a.slug })) : [{ slug: "__placeholder__" }];
}

export async function generateMetadata({ params }: PageProps<"/dokumentasi/[slug]">): Promise<Metadata> {
  const a = await getActivity((await params).slug);
  return a ? { title: a.title, description: a.summary ?? undefined, openGraph: a.cover ? { images: [a.cover] } : undefined } : {};
}

/** Ubah link YouTube biasa menjadi URL embed; selain YouTube ditampilkan sebagai tautan. */
function youtubeEmbed(url: string) {
  const m = url.match(/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|shorts\/|embed\/))([\w-]{11})/);
  return m ? `https://www.youtube-nocookie.com/embed/${m[1]}` : null;
}

export default async function ActivityPage({ params }: PageProps<"/dokumentasi/[slug]">) {
  const a = await getActivity((await params).slug);
  if (!a) notFound();

  const photos = a.photos ?? [];
  const videos = a.videos ?? [];

  return (
    <article className="mx-auto max-w-6xl px-4 py-12 sm:px-6">
      <Link href="/dokumentasi" className="font-semibold text-ink-soft hover:text-grape">
        ← Semua dokumentasi
      </Link>

      <header className="mt-6 max-w-3xl">
        <span className={`chip ${a.type === "event" ? "bg-grape text-white" : "bg-peri"}`}>{a.type_label}</span>
        <p className="mt-3 font-hand text-3xl text-grape">{formatDate(a.held_on, { weekday: "long" })}</p>
        <h1 className="font-display text-4xl font-semibold leading-tight sm:text-5xl">{a.title}</h1>
        {a.summary && <p className="mt-3 text-lg text-ink-soft">{a.summary}</p>}
      </header>

      {a.cover && (
        <div className="relative mt-8 aspect-[16/9] overflow-hidden rounded-3xl border-2 border-ink shadow-[6px_6px_0_0_var(--color-ink)]">
          <Image src={a.cover} alt={a.title} fill priority sizes="(min-width: 1152px) 1152px, 100vw" className="object-cover" />
        </div>
      )}

      {a.description && <RichText html={a.description} className="mt-10 max-w-3xl text-lg" />}

      {(photos.length > 0 || a.album_url) && (
        <section className="mt-14">
          <div className="flex flex-wrap items-end justify-between gap-4">
            <h2 className="font-display text-2xl">Galeri foto</h2>
            {a.album_url && (
              <a href={a.album_url} target="_blank" rel="noreferrer" className="btn btn-sun text-sm">
                📁 Lihat album lengkap
              </a>
            )}
          </div>
          {photos.length > 0 && (
            <div className="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3">
              {photos.map((src, i) => (
                <a
                  key={src}
                  href={src}
                  target="_blank"
                  rel="noreferrer"
                  className={`relative block aspect-square overflow-hidden rounded-2xl border-2 border-ink ${i % 5 === 0 ? "sm:col-span-2 sm:row-span-2" : ""}`}
                >
                  <Image
                    src={src}
                    alt={`Foto ${i + 1} — ${a.title}`}
                    fill
                    sizes="(min-width: 640px) 33vw, 50vw"
                    className="object-cover transition-transform duration-300 hover:scale-105"
                  />
                </a>
              ))}
            </div>
          )}
        </section>
      )}

      {videos.length > 0 && (
        <section className="mt-14">
          <h2 className="font-display text-2xl font-semibold">Video</h2>
          <div className="mt-6 grid gap-6 md:grid-cols-2">
            {videos.map((v) => {
              const embed = youtubeEmbed(v.url);
              return embed ? (
                <div key={v.url} className="card overflow-hidden">
                  <iframe
                    src={embed}
                    title={v.title ?? a.title}
                    className="aspect-video w-full"
                    loading="lazy"
                    allow="accelerometer; encrypted-media; gyroscope; picture-in-picture"
                    allowFullScreen
                  />
                  {v.title && <p className="p-4 font-semibold">{v.title}</p>}
                </div>
              ) : (
                <a key={v.url} href={v.url} target="_blank" rel="noreferrer" className="card flex items-center gap-4 bg-coral-soft p-6 hover:-translate-y-1">
                  <span className="text-4xl">▶️</span>
                  <span className="font-semibold">{v.title ?? "Tonton video di Instagram"}</span>
                </a>
              );
            })}
          </div>
        </section>
      )}
    </article>
  );
}
