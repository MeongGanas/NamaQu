import type { Metadata } from "next";
import Image from "next/image";
import Link from "next/link";
import { notFound } from "next/navigation";
import { Difficulty } from "@/components/cards";
import { RichText, Star } from "@/components/decor";
import { getModule, getModules } from "@/lib/api";

// Halaman detail sudah di-prerender; slug yang belum dikenal boleh menunggu render agar status 404 tetap benar.
export const instant = false;

export async function generateStaticParams() {
  const items = await getModules();
  return items.length ? items.map((m) => ({ slug: m.slug })) : [{ slug: "__placeholder__" }];
}

export async function generateMetadata({ params }: PageProps<"/modul/[slug]">): Promise<Metadata> {
  const m = await getModule((await params).slug);
  return m ? { title: m.title, description: m.summary ?? undefined } : {};
}

export default async function ModulePage({ params }: PageProps<"/modul/[slug]">) {
  const m = await getModule((await params).slug);
  if (!m) notFound();

  return (
    <article className="mx-auto max-w-4xl px-4 py-12 sm:px-6">
      <Link href="/modul" className="font-semibold text-ink-soft hover:text-grape">
        ← Semua modul
      </Link>

      <div className="mt-6 flex flex-wrap gap-2">
        <span className="chip bg-tangerine">{m.level_label}</span>
        <span className="chip bg-white">{m.category_label}</span>
      </div>
      <h1 className="mt-3 font-display text-4xl font-semibold leading-tight sm:text-5xl">{m.title}</h1>
      {m.summary && <p className="mt-3 text-lg text-ink-soft">{m.summary}</p>}

      <dl className="card mt-8 grid gap-4 bg-peri-soft p-6 sm:grid-cols-2">
        <div>
          <dt className="text-sm font-semibold text-ink-soft">Tingkat kesulitan</dt>
          <dd className="mt-1">
            <Difficulty value={m.difficulty} label={m.difficulty_label} />
          </dd>
        </div>
        {m.target && (
          <div>
            <dt className="text-sm font-semibold text-ink-soft">Target usia / kemampuan</dt>
            <dd className="mt-1 font-semibold">{m.target}</dd>
          </div>
        )}
      </dl>

      {m.cover && (
        <div className="relative mt-8 aspect-[16/9] overflow-hidden rounded-3xl border-2 border-ink">
          <Image src={m.cover} alt="" fill sizes="(min-width: 896px) 896px, 100vw" className="object-cover" />
        </div>
      )}

      {m.objectives.length > 0 && (
        <section className="mt-10">
          <h2 className="font-display text-2xl font-semibold">Tujuan pembelajaran</h2>
          <ul className="mt-4 space-y-3">
            {m.objectives.map((o) => (
              <li key={o} className="flex items-start gap-3">
                <Star className="mt-0.5 h-5 w-5 shrink-0" color="var(--color-tangerine)" />
                <span>{o}</span>
              </li>
            ))}
          </ul>
        </section>
      )}

      {m.content && (
        <section className="mt-10">
          <h2 className="font-display text-2xl font-semibold">Materi</h2>
          <RichText html={m.content} className="mt-2" />
        </section>
      )}

      {m.external_url && (
        <a href={m.external_url} target="_blank" rel="noreferrer" className="btn btn-primary mt-8">
          🎮 Buka website game coding
        </a>
      )}

      {m.gallery && m.gallery.length > 0 && (
        <section className="mt-12">
          <h2 className="font-display text-2xl font-semibold">Dokumentasi</h2>
          <div className="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3">
            {m.gallery.map((src) => (
              <div key={src} className="relative aspect-square overflow-hidden rounded-2xl border-2 border-ink">
                <Image src={src} alt="" fill sizes="(min-width: 640px) 33vw, 50vw" className="object-cover" />
              </div>
            ))}
          </div>
        </section>
      )}
    </article>
  );
}
