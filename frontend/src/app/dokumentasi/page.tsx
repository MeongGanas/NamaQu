import type { Metadata } from "next";
import { PageHero } from "@/components/decor";
import { getActivities } from "@/lib/api";
import { ActivityBrowser } from "./activity-browser";

export const metadata: Metadata = {
  title: "Dokumentasi Kegiatan",
  description: "Rekam jejak kegiatan belajar mingguan dan event sosial NamaQu di basecamp Keputih.",
};

export default async function ActivitiesPage() {
  const activities = await getActivities();

  return (
    <>
      <PageHero eyebrow="album kenangan" title={<>Dokumentasi <span className="text-sun">kegiatan</span></>} tone="sky">
        Rekam jejak kegiatan belajar setiap Minggu dan event spesial bersama adik-adik NamaQu.
      </PageHero>
      <section className="mx-auto max-w-6xl px-4 py-14 sm:px-6">
        <ActivityBrowser activities={activities} />
      </section>
    </>
  );
}
