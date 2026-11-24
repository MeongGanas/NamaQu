import type { Metadata } from "next";
import Image from "next/image";
import { EmptyState, PageHero, RichText, Sparkle, Star } from "@/components/decor";
import { getSite, waLink } from "@/lib/api";

export const metadata: Metadata = {
  title: "Terms & Conditions Kolaborasi",
  description: "Ketentuan kerja sama (partnership) bagi instansi, organisasi, dan komunitas yang ingin berkolaborasi dengan NamaQu.",
};

const TONES = ["bg-coral-soft", "bg-tangerine-soft", "bg-peri-soft", "bg-sun/40", "bg-lilac/40"];

export default async function CollaborationPage() {
  const site = await getSite();
  const cp = site.collab_contact;
  const wa = waLink(cp?.whatsapp, "Halo NamaQu, saya ingin mengajukan kerja sama.");
  const terms = site.collab_terms ?? [];

  return (
    <>
      <PageHero
        eyebrow="terms & conditions · partnership"
        title={<>Berkolaborasi dengan <span className="text-sun">NamaQu</span></>}
        tone="sky"
      >
        {site.collab_intro}
      </PageHero>

      <section className="mx-auto max-w-6xl px-4 py-14 sm:px-6">
        {terms.length ? (
          <ol className="grid gap-6 md:grid-cols-2">
            {terms.map((t, i) => (
              <li key={t.title} className={`card relative flex flex-col p-6 sm:p-8 ${TONES[i % TONES.length]}`}>
                <span className="absolute -right-3 -top-4 flex h-12 w-12 items-center justify-center rounded-full border-2 border-ink bg-sun font-display text-2xl font-bold shadow-[2px_2px_0_0_var(--color-ink)]">
                  {i + 1}
                </span>
                <h2 className="pr-8 font-display text-2xl font-semibold leading-tight">{t.title}</h2>
                {t.optional && <span className="chip mt-2 self-start bg-white">Opsional</span>}
                <RichText html={t.content} className="flex-1" />
                {t.note && (
                  <p className="mt-2 flex items-start gap-2 border-t-2 border-dashed border-ink/20 pt-3 text-sm font-semibold">
                    <Star className="mt-0.5 h-4 w-4 shrink-0" />
                    {t.note}
                  </p>
                )}
              </li>
            ))}
          </ol>
        ) : (
          <EmptyState>Ketentuan kerja sama akan segera ditampilkan.</EmptyState>
        )}

        <div className="card relative mt-14 flex flex-col items-start gap-5 overflow-hidden bg-grape p-8 text-white sm:flex-row sm:items-center sm:justify-between">
          <Sparkle className="absolute right-6 top-4 h-8 w-8" color="var(--color-sun)" />
          <div className="flex items-center gap-4">
            <span className="hidden rounded-2xl bg-white p-1.5 sm:block">
              <Image src="/logo-namaqu.png" alt="" width={56} height={56} />
            </span>
            <div>
              <p className="font-hand text-2xl text-sun">contact person kerja sama</p>
              <p className="font-display text-2xl font-semibold">{cp?.name ?? "Pengurus NamaQu"}</p>
              <p className="text-white/80">Thanks for being part of this journey!</p>
            </div>
          </div>
          {wa ? (
            <a href={wa} target="_blank" rel="noreferrer" className="btn btn-light">
              Ajukan kerja sama via WhatsApp
            </a>
          ) : (
            <a href="/kontak" className="btn btn-light">
              Lihat kontak
            </a>
          )}
        </div>
      </section>
    </>
  );
}
