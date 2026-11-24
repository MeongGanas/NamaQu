import type { Metadata } from "next";
import Link from "next/link";
import { TestimonialCard } from "@/components/cards";
import { EmptyState, PageHero } from "@/components/decor";
import { getTestimonials } from "@/lib/api";

export const metadata: Metadata = {
  title: "Kesan & Pesan Volunteer",
  description: "Cerita pengalaman kakak-kakak volunteer selama mengikuti kegiatan NamaQu.",
};

export default async function TestimonialsPage() {
  const items = await getTestimonials();

  return (
    <>
      <PageHero eyebrow="dari hati" title={<>Kesan & pesan <span className="text-sun">volunteer</span></>}>
        Pengalaman kakak-kakak yang sudah ikut belajar bareng adik-adik NamaQu.
      </PageHero>
      <section className="mx-auto max-w-6xl px-4 py-14 sm:px-6">
        {items.length ? (
          <div className="columns-1 gap-6 md:columns-2 lg:columns-3 [&>*]:mb-6 [&>*]:break-inside-avoid">
            {items.map((t, i) => (
              <TestimonialCard key={t.id} t={t} i={i} />
            ))}
          </div>
        ) : (
          <EmptyState>Belum ada kesan & pesan yang ditampilkan.</EmptyState>
        )}
        <div className="mt-12 text-center">
          <p className="font-hand text-3xl">giliran kamu?</p>
          <Link href="/volunteer" className="btn btn-primary mt-3">
            Daftar jadi volunteer
          </Link>
        </div>
      </section>
    </>
  );
}
