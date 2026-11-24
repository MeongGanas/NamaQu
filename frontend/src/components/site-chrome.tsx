import Link from "next/link";
import { getSite, igLink, tiktokLink, waLink } from "@/lib/api";
import Image from "next/image";
import { Heart } from "./decor";

const NAV = [
  { href: "/volunteer", label: "Volunteer" },
  { href: "/modul", label: "Modul Belajar" },
  { href: "/dokumentasi", label: "Dokumentasi" },
  { href: "/tentang", label: "Tentang Kami" },
  { href: "/kolaborasi", label: "Kolaborasi" },
  { href: "/kontak", label: "Kontak" },
];

export function Logo({ className = "" }: { className?: string }) {
  return (
    <Link href="/" className={`group inline-flex items-center gap-2 ${className}`} aria-label="NamaQu — beranda">
      <Image
        src="/logo-namaqu.png"
        alt=""
        width={44}
        height={44}
        priority
        className="h-11 w-11 transition-transform group-hover:-rotate-6"
      />
      <span className="font-display text-2xl font-bold tracking-tight">
        Nama<span className="text-grape">Qu</span>
      </span>
    </Link>
  );
}

export function Header() {
  return (
    <header className="sticky top-0 z-40 border-b-2 border-ink bg-paper/90 backdrop-blur">
      <div className="mx-auto flex h-16 max-w-6xl items-center justify-between gap-4 px-4 sm:px-6">
        <Logo />
        <nav className="hidden items-center gap-0.5 xl:flex" aria-label="Utama">
          {NAV.map((n) => (
            <Link
              key={n.href}
              href={n.href}
              className="whitespace-nowrap rounded-full px-2.5 py-1.5 text-sm font-semibold text-ink-soft transition-colors hover:bg-coral-soft hover:text-ink"
            >
              {n.label}
            </Link>
          ))}
          <Link href="/volunteer" className="btn btn-primary ml-2 py-1.5 text-sm">
            Jadi Volunteer
          </Link>
        </nav>

        {/* menu HP tanpa JavaScript */}
        <details className="group relative xl:hidden">
          <summary className="btn btn-light cursor-pointer list-none px-4 py-1.5 text-sm [&::-webkit-details-marker]:hidden">
            <span className="group-open:hidden">Menu</span>
            <span className="hidden group-open:inline">Tutup</span>
          </summary>
          <nav
            className="card absolute right-0 mt-3 flex w-60 flex-col gap-1 p-3"
            aria-label="Utama (seluler)"
          >
            {NAV.map((n) => (
              <Link key={n.href} href={n.href} className="rounded-xl px-3 py-2 font-semibold hover:bg-coral-soft">
                {n.label}
              </Link>
            ))}
          </nav>
        </details>
      </div>
    </header>
  );
}

export async function Footer() {
  const site = await getSite();
  const ig = igLink(site.instagram);

  return (
    <footer className="night-sky mt-24 border-t-2 border-ink text-paper">
      <div className="mx-auto grid max-w-6xl gap-10 px-4 py-14 sm:px-6 md:grid-cols-[1.4fr_1fr_1fr]">
        <div>
          <div className="flex items-center gap-3">
            <span className="rounded-2xl bg-paper p-1">
              <Image src="/logo-namaqu.png" alt="" width={52} height={52} />
            </span>
            <p className="font-display text-3xl font-bold">
              Komunitas Nama<span className="text-sun">Qu</span>
            </p>
          </div>
          <p className="mt-3 max-w-sm text-paper/75">
            Komunitas sosial & pendidikan untuk anak-anak SD di {site.basecamp_address ?? "Keputih, Surabaya"}.
          </p>
          <p className="mt-4 font-hand text-2xl text-coral">{site.tagline ?? "Sebut Namamu, Gapai Cita-Citamu!"} ♡</p>
        </div>

        <div>
          <p className="mb-3 text-sm font-bold uppercase tracking-widest text-paper/50">Jelajahi</p>
          <ul className="space-y-2">
            {NAV.map((n) => (
              <li key={n.href}>
                <Link href={n.href} className="hover:text-coral">
                  {n.label}
                </Link>
              </li>
            ))}
            <li>
              <Link href="/kesan-pesan" className="hover:text-coral">
                Kesan & Pesan
              </Link>
            </li>
          </ul>
        </div>

        <div>
          <p className="mb-3 text-sm font-bold uppercase tracking-widest text-paper/50">Hubungi</p>
          <ul className="space-y-2">
            {ig && (
              <li>
                <a href={ig} target="_blank" rel="noreferrer" className="hover:text-coral">
                  Instagram @{site.instagram}
                </a>
              </li>
            )}
            {site.tiktok && (
              <li>
                <a href={tiktokLink(site.tiktok) ?? "#"} target="_blank" rel="noreferrer" className="hover:text-coral">
                  TikTok @{site.tiktok}
                </a>
              </li>
            )}
            {site.linkedin && (
              <li>
                <a href={site.linkedin} target="_blank" rel="noreferrer" className="hover:text-coral">
                  LinkedIn
                </a>
              </li>
            )}
            {(site.contacts ?? []).map((c) => (
              <li key={c.whatsapp}>
                <a href={waLink(c.whatsapp) ?? "#"} target="_blank" rel="noreferrer" className="hover:text-coral">
                  {c.name}
                  {c.purpose ? <span className="text-paper/50"> · {c.purpose}</span> : null}
                </a>
              </li>
            ))}
          </ul>
        </div>
      </div>
      <div className="border-t border-paper/15">
        <p className="mx-auto flex max-w-6xl items-center gap-2 px-4 py-5 text-sm text-paper/50 sm:px-6">
          <Heart className="h-4 w-4" /> NamaQu · Basecamp Keputih, Surabaya
        </p>
      </div>
    </footer>
  );
}
