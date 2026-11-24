import "server-only";
import { cacheLife, cacheTag } from "next/cache";
import type { Activity, LearningModule, Recruitment, Site, TeamMember, Testimonial } from "./shared";

export * from "./shared";

/**
 * Sumber data:
 * - API Laravel di server rumah (STB Armbian) bila API_URL / NEXT_PUBLIC_API_URL di-set.
 *   Hasilnya di-cache di Vercel, jadi STB hanya kena request sesekali dan
 *   website tetap tampil (versi cache terakhir) walau STB sedang offline.
 * - Data statis bawaan (src/lib/static-data.ts) bila belum ada backend, atau DATA_SOURCE=static.
 *   Dipakai untuk mempublikasikan desain lebih dulu.
 */
const RAW_API_URL = process.env.API_URL ?? process.env.NEXT_PUBLIC_API_URL ?? "";
const API_URL = RAW_API_URL.replace(/\/$/, "");

export const IS_STATIC = process.env.DATA_SOURCE === "static" || !API_URL;

export const CACHE_TAG = "namaqu";

/**
 * Melempar error bila API tidak bisa dihubungi: Next.js lalu tetap menyajikan
 * versi cache sebelumnya (stale-while-revalidate) alih-alih halaman kosong.
 * 404 dikembalikan sebagai null supaya halaman bisa memanggil notFound().
 */
async function get<T>(path: string): Promise<T | null> {
  if (IS_STATIC) {
    const { staticGet } = await import("./static-data");
    return (staticGet(path) as T | null) ?? null;
  }
  const res = await fetch(`${API_URL}${path}`, { headers: { Accept: "application/json" } });
  if (res.status === 404) return null;
  if (!res.ok) throw new Error(`API ${path} -> ${res.status}`);
  const json = (await res.json()) as { data: T };
  return json.data;
}

export async function getSite() {
  "use cache";
  cacheTag(CACHE_TAG);
  cacheLife("hours");
  return (await get<Site>("/site"))!;
}

export async function getRecruitments() {
  "use cache";
  cacheTag(CACHE_TAG);
  cacheLife("hours");
  return (await get<Recruitment[]>("/recruitments")) ?? [];
}

export async function getRecruitment(slug: string) {
  "use cache";
  cacheTag(CACHE_TAG);
  cacheLife("hours");
  return get<Recruitment>(`/recruitments/${encodeURIComponent(slug)}`);
}

export async function getModules() {
  "use cache";
  cacheTag(CACHE_TAG);
  cacheLife("hours");
  return (await get<LearningModule[]>("/modules")) ?? [];
}

export async function getModule(slug: string) {
  "use cache";
  cacheTag(CACHE_TAG);
  cacheLife("hours");
  return get<LearningModule>(`/modules/${encodeURIComponent(slug)}`);
}

export async function getActivities() {
  "use cache";
  cacheTag(CACHE_TAG);
  cacheLife("hours");
  return (await get<Activity[]>("/activities?per_page=48")) ?? [];
}

export async function getActivity(slug: string) {
  "use cache";
  cacheTag(CACHE_TAG);
  cacheLife("hours");
  return get<Activity>(`/activities/${encodeURIComponent(slug)}`);
}

export async function getTeam() {
  "use cache";
  cacheTag(CACHE_TAG);
  cacheLife("hours");
  return (await get<TeamMember[]>("/team")) ?? [];
}

export async function getTestimonials() {
  "use cache";
  cacheTag(CACHE_TAG);
  cacheLife("hours");
  return (await get<Testimonial[]>("/testimonials")) ?? [];
}

/** Dipakai server action pendaftaran (tidak di-cache). */
export async function postRegistration(slug: string, body: Record<string, string>) {
  if (IS_STATIC) {
    return {
      ok: false,
      message:
        "Pendaftaran online belum aktif di versi pratinjau ini. Untuk mendaftar, hubungi narahubung NamaQu lewat WhatsApp di halaman Kontak, ya!",
      errors: undefined as Record<string, string[]> | undefined,
    };
  }
  const res = await fetch(`${API_URL}/recruitments/${encodeURIComponent(slug)}/register`, {
    method: "POST",
    headers: { Accept: "application/json", "Content-Type": "application/json" },
    body: JSON.stringify(body),
    cache: "no-store",
  });
  const json = (await res.json().catch(() => ({}))) as { message?: string; errors?: Record<string, string[]> };
  return { ok: res.ok, ...json };
}
