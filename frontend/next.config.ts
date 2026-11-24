import type { NextConfig } from "next";

// Kosong = mode pratinjau dengan data statis (frontend di-host tanpa backend).
const apiUrl = process.env.NEXT_PUBLIC_API_URL ?? process.env.API_URL ?? "";
const apiOrigin = apiUrl ? new URL(apiUrl) : null;

// Panel admin (Filament) ada di backend; /admin di website diarahkan ke sana.
const adminUrl = apiUrl.replace(/\/api\/?$/, "/admin");

const nextConfig: NextConfig = {
  async redirects() {
    if (!adminUrl) return [];
    return [
      { source: "/admin", destination: adminUrl, permanent: false },
      { source: "/login", destination: adminUrl, permanent: false },
    ];
  },
  cacheComponents: true,
  partialPrefetching: true,
  turbopack: {
    rules: {
      // hanya CSS milik project; CSS internal next/font ("foreign") tidak boleh ikut diproses Tailwind
      "*.css": {
        condition: { not: "foreign" },
        loaders: ["@tailwindcss/turbopack"],
        as: "*.css",
      },
    },
  },
  images: {
    // Di production gambar dioptimasi & di-cache Vercel, jadi STB jarang dimintai file.
    // Di lokal (backend di localhost/docker) optimasi dimatikan.
    unoptimized: process.env.NODE_ENV !== "production",
    remotePatterns: [
      // foto konten di production disimpan & disajikan dari Cloudinary
      { protocol: "https", hostname: "res.cloudinary.com", pathname: "/**" },
      ...(apiOrigin
        ? [
            {
              protocol: apiOrigin.protocol.replace(":", "") as "http" | "https",
              hostname: apiOrigin.hostname,
              port: apiOrigin.port,
              pathname: "/**",
            },
          ]
        : []),
    ],
    formats: ["image/webp"],
    minimumCacheTTL: 60 * 60 * 24 * 30,
  },
};

export default nextConfig;
