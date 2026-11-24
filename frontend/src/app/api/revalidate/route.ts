import { revalidateTag } from "next/cache";
import { CACHE_TAG } from "@/lib/api";

/**
 * Dipanggil backend Laravel setiap kali pengurus menyimpan konten di admin,
 * supaya perubahan langsung tampil tanpa menunggu cache kedaluwarsa.
 *   POST /api/revalidate   header: x-revalidate-secret: <REVALIDATE_SECRET>
 */
export async function POST(request: Request) {
  const secret = process.env.REVALIDATE_SECRET;
  if (!secret || request.headers.get("x-revalidate-secret") !== secret) {
    return Response.json({ ok: false }, { status: 401 });
  }
  revalidateTag(CACHE_TAG, "max");
  return Response.json({ ok: true });
}
