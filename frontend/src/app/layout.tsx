import type { Metadata } from "next";
import { Lilita_One, Poppins } from "next/font/google";
import { Footer, Header } from "@/components/site-chrome";
import "./globals.css";

// Judul: tebal & membulat seperti "TERMS AND CONDITION" di materi brand.
const lilita = Lilita_One({
  variable: "--font-lilita",
  subsets: ["latin"],
  weight: "400",
});

// Teks: Poppins, sama dengan materi brand NamaQu.
const poppins = Poppins({
  variable: "--font-poppins",
  subsets: ["latin"],
  weight: ["400", "500", "600", "700"],
});

export const metadata: Metadata = {
  metadataBase: new URL(process.env.NEXT_PUBLIC_SITE_URL ?? "http://localhost:3010"),
  title: {
    default: "NamaQu — Komunitas Belajar Anak SD di Keputih, Surabaya",
    template: "%s · NamaQu",
  },
  description:
    "NamaQu adalah komunitas sosial & pendidikan yang mengajak volunteer belajar bersama anak-anak SD di basecamp Keputih, Surabaya.",
  openGraph: {
    siteName: "NamaQu",
    locale: "id_ID",
    type: "website",
  },
};

export default function RootLayout({ children }: LayoutProps<"/">) {
  return (
    <html lang="id" data-scroll-behavior="smooth" className={`${lilita.variable} ${poppins.variable} h-full antialiased`}>
      <body className="flex min-h-full flex-col">
        <a href="#isi" className="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 btn btn-light">
          Langsung ke isi
        </a>
        <Header />
        <main id="isi" className="flex-1">
          {children}
        </main>
        <Footer />
      </body>
    </html>
  );
}
