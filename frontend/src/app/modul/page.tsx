import type { Metadata } from "next";
import { PageHero } from "@/components/decor";
import { getModules } from "@/lib/api";
import { ModuleBrowser } from "./module-browser";

export const metadata: Metadata = {
  title: "Modul Pembelajaran",
  description: "Materi calistung, logika & matematika, dan game coding yang dipelajari anak-anak di NamaQu.",
};

const LEVEL_INFO = [
  { title: "Tingkat Kecil", text: "Untuk adik-adik yang baru mulai mengenal huruf & angka.", tone: "bg-tangerine" },
  { title: "Tingkat Sedang", text: "Sudah bisa membaca & berhitung dasar, mulai berlatih pola dan logika.", tone: "bg-peri" },
  { title: "Tingkat Besar", text: "Siap dengan soal cerita, teka-teki logika, dan coding.", tone: "bg-lilac" },
];

export default async function ModulesPage() {
  const modules = await getModules();

  return (
    <>
      <PageHero eyebrow="untuk orang tua & volunteer" title={<>Apa saja yang <span className="text-sun">dipelajari</span> adik-adik?</>} tone="mint">
        Semua materi yang dipakai di kegiatan NamaQu bisa dilihat di sini. Pengelompokan tingkat tidak hanya berdasarkan
        usia, tetapi juga kemampuan dan tingkat kesulitan materi.
      </PageHero>

      <section className="mx-auto max-w-6xl px-4 py-14 sm:px-6">
        <div className="mb-12 grid gap-4 md:grid-cols-3">
          {LEVEL_INFO.map((l, i) => (
            <div key={l.title} className={`card p-5 ${l.tone} ${["-rotate-1", "rotate-1", "-rotate-1"][i]}`}>
              <h2 className="font-display text-xl font-semibold">{l.title}</h2>
              <p className="mt-1 text-sm text-ink-soft">{l.text}</p>
            </div>
          ))}
        </div>
        <ModuleBrowser modules={modules} />
      </section>
    </>
  );
}
