import Link from "next/link";
import { Star } from "@/components/decor";

export default function NotFound() {
  return (
    <section className="mx-auto flex max-w-xl flex-col items-center px-4 py-24 text-center">
      <Star className="h-16 w-16 animate-wiggle" />
      <p className="mt-6 font-hand text-4xl text-grape">ups, nyasar!</p>
      <h1 className="font-display text-4xl font-semibold">Halaman tidak ditemukan</h1>
      <p className="mt-3 text-ink-soft">Mungkin halamannya sudah dipindah atau belum dipublikasikan.</p>
      <Link href="/" className="btn btn-primary mt-8">
        Kembali ke beranda
      </Link>
    </section>
  );
}
