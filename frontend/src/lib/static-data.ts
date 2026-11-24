import "server-only";
import type { Activity, CollabTerm, LearningModule, Recruitment, Site, TeamMember, Testimonial } from "./shared";

/**
 * Data statis untuk mode pratinjau (frontend di-host tanpa backend).
 * Konten pengurus, testimoni, kontak & T&C adalah data asli NamaQu
 * (dari "SEEDER KOLEKTIF" & "TNC NamaQu.pdf"); open recruitment, modul
 * dan dokumentasi masih CONTOH sampai pengurus mengisinya lewat admin.
 */

const day = 24 * 60 * 60 * 1000;
const iso = (d: Date) => d.toISOString().slice(0, 10);
const addDays = (n: number) => iso(new Date(Date.now() + n * day));
const nextWeekday = (weekday: number) => {
  const d = new Date();
  d.setDate(d.getDate() + ((weekday - d.getDay() + 7) % 7 || 7));
  return iso(d);
};

const collabTerms: CollabTerm[] = [
  { title: "Bentuk Kerja Sama", optional: false, note: null, content: "<p>Bentuk kerja sama dari kolaborator adalah melakukan kegiatan yang akan diadakan di <strong>basecamp NamaQu</strong>.</p>" },
  { title: "Penggunaan Logo", optional: false, note: "Butuh logo? Tinggal reach out ke tim kita ya 😉", content: "<p>Kolaborator diharapkan <strong>mencantumkan logo NamaQu</strong> pada seluruh media publikasi kegiatan, seperti poster, banner, dan konten media sosial.</p>" },
  { title: "Komitmen Jadwal & Lokasi", optional: false, note: null, content: "<p>Kolaborator diharapkan menjaga <strong>komitmen terkait tempat, tanggal, dan waktu</strong> pelaksanaan yang telah disepakati bersama.</p>" },
  { title: "Perubahan dan Pembatalan Kegiatan", optional: false, note: "Perubahan dan pembatalan maksimal dilakukan 7 hari sebelum hari pelaksanaan.", content: "<p>Diharapkan tidak melakukan perubahan dan pembatalan kegiatan secara sepihak dalam waktu mendadak.</p>" },
  { title: "Kontribusi Operasional", optional: false, note: null, content: "<p>Kolaborator bersedia <strong>memberikan donasi (tanpa nominal yang ditentukan)</strong>, yang akan digunakan untuk kebutuhan seperti kebersihan dan perlengkapan belajar mengajar.</p>" },
  { title: "Pemberian Cenderamata", optional: true, note: null, content: "<p>Apabila kolaborator ingin memberikan cenderamata seperti sertifikat, plakat, atau bentuk penghargaan lainnya, dimohon mencantumkan nama komunitas <strong>“Komunitas NamaQu”</strong>.</p>" },
  { title: "Menjaga Nama Baik Komunitas", optional: false, note: null, content: "<p>Kolaborator diharapkan turut <strong>menjaga nama baik Komunitas NamaQu</strong> serta tidak melakukan tindakan yang dapat merugikan atau mencoreng reputasi bersama, baik selama maupun setelah kegiatan berlangsung.</p>" },
  { title: "Force Majeure", optional: false, note: null, content: "<p>Dalam kondisi di luar kendali (seperti cuaca ekstrem, bencana alam, atau situasi darurat lainnya), jadwal kegiatan dapat disesuaikan kembali berdasarkan kesepakatan bersama melalui komunikasi yang baik antara kedua belah pihak.</p>" },
  { title: "Dress Code & Attitude", optional: false, note: null, content: "<p>Demi menciptakan lingkungan yang nyaman dan positif, khususnya bagi anak-anak, kolaborator diharapkan <strong>mengenakan pakaian yang sopan serta menjaga sikap yang ramah</strong> dan profesional selama kegiatan berlangsung. Kolaborator juga <strong>dilarang merokok</strong> maupun <strong>menggunakan vape</strong> di lingkungan belajar adik-adik.</p>" },
];

const recruitments: Recruitment[] = [
  {
    id: 1,
    slug: "volunteer-minggu-ini",
    type: "weekly",
    type_label: "Volunteer Mingguan",
    title: "Volunteer Minggu Ini",
    summary: "Temani adik-adik belajar calistung & logika di basecamp Keputih.",
    description: "<p>Kegiatan belajar rutin setiap hari Minggu. Volunteer akan mendampingi adik-adik dalam kelompok kecil sesuai tingkat (Kecil, Sedang, Besar).</p>",
    requirements: ["Follow & like postingan Instagram @namaqu.id", "Balas Story open recruitment", "Hadir tepat waktu pukul 07.45"],
    activity_date: nextWeekday(0),
    activity_time: "08.00 - 11.00 WIB",
    location: "Basecamp NamaQu, Keputih, Surabaya",
    registration_opens_at: null,
    registration_closes_at: nextWeekday(6),
    quota: 10,
    registration_method: "instagram",
    external_url: "https://www.instagram.com/namaqu.id/",
    poster: null,
    is_open: true,
  },
  {
    id: 2,
    slug: "volunteer-hari-anak",
    type: "event",
    type_label: "Volunteer Kegiatan Besar",
    title: "Volunteer Perayaan Hari Anak",
    summary: "Bantu kami menyiapkan lomba, panggung, dan games untuk 40+ anak.",
    description: "<p>Perayaan Hari Anak bersama adik-adik NamaQu. Volunteer akan dibagi ke divisi acara, perlengkapan, konsumsi, dan dokumentasi.</p>",
    requirements: ["Mahasiswa/umum usia 17+", "Bisa hadir technical meeting H-3", "Bersedia mengikuti seluruh rangkaian acara"],
    activity_date: addDays(21),
    activity_time: "07.00 - 13.00 WIB",
    location: "Basecamp NamaQu, Keputih, Surabaya",
    registration_opens_at: addDays(-2),
    registration_closes_at: addDays(14),
    quota: 25,
    registration_method: "form",
    external_url: null,
    poster: null,
    is_open: true,
  },
];

const moduleRows: [string, LearningModule["level"], LearningModule["category"], number, string, string[]][] = [
  ["Mengenal Huruf & Suku Kata", "kecil", "calistung", 1, "Usia 6-7 tahun, belum lancar membaca", ["Mengenal huruf vokal & konsonan", "Membaca suku kata KV"]],
  ["Penjumlahan dengan Benda", "kecil", "calistung", 1, "Kelas 1 SD", ["Menjumlahkan bilangan 1-20 dengan benda konkret"]],
  ["Pola & Urutan", "sedang", "logika_matematika", 2, "Kelas 2-3 SD", ["Menemukan pola gambar & bilangan", "Melanjutkan barisan sederhana"]],
  ["Perkalian Lewat Permainan", "sedang", "logika_matematika", 2, "Kelas 3 SD", ["Memahami perkalian sebagai penjumlahan berulang"]],
  ["Teka-teki Logika", "besar", "logika_matematika", 3, "Kelas 4-6 SD", ["Bernalar dengan petunjuk", "Menyelesaikan soal cerita"]],
  ["Coding Pertama dengan Blok", "besar", "game_coding", 2, "Kelas 4-6 SD, sudah lancar membaca", ["Mengenal urutan perintah (sequence)", "Mengenal perulangan (loop)"]],
];

const LEVELS = { kecil: "Tingkat Kecil", sedang: "Tingkat Sedang", besar: "Tingkat Besar" } as const;
const CATEGORIES = { calistung: "Calistung", logika_matematika: "Logika & Matematika", game_coding: "Game Coding" } as const;
const DIFFICULTIES = { 1: "Mudah", 2: "Sedang", 3: "Menantang" } as const;

const slugify = (s: string) =>
  s.toLowerCase().replace(/&/g, "").replace(/[^a-z0-9]+/g, "-").replace(/^-|-$/g, "");

const modules: LearningModule[] = moduleRows.map(([title, level, category, difficulty, target, objectives], i) => ({
  id: i + 1,
  slug: slugify(title),
  title,
  summary: `Materi ${CATEGORIES[category].toLowerCase()} untuk ${LEVELS[level].toLowerCase()}.`,
  level,
  level_label: LEVELS[level],
  category,
  category_label: CATEGORIES[category],
  difficulty,
  difficulty_label: DIFFICULTIES[difficulty as 1 | 2 | 3],
  target,
  objectives,
  external_url: category === "game_coding" ? "https://code.org" : null,
  cover: null,
  content: "<p>Kegiatan dibuka dengan ice breaking, lalu adik-adik belajar dalam kelompok kecil didampingi kakak volunteer.</p>",
  gallery: [],
}));

const activities: Activity[] = (
  [
    ["Belajar Minggu: Calistung Ceria", "weekly", 7],
    ["Belajar Minggu: Pola & Bilangan", "weekly", 14],
    ["Belajar Minggu: Coding Tanpa Komputer", "weekly", 21],
    ["Perayaan Hari Anak", "event", 120],
  ] as const
).map(([title, type, daysAgo], i) => ({
  id: i + 1,
  slug: slugify(title),
  title,
  type,
  type_label: type === "event" ? "Event Khusus" : "Kegiatan Mingguan",
  held_on: addDays(-daysAgo),
  summary: "Keseruan adik-adik NamaQu belajar bareng kakak volunteer.",
  cover: null,
  photo_count: 0,
  description: "<p>Adik-adik belajar dalam tiga kelompok sesuai tingkat: kecil, sedang, dan besar. Foto dokumentasi akan ditambahkan pengurus.</p>",
  photos: [],
  videos: [],
  album_url: null,
}));

const team: TeamMember[] = (
  [
    ["Nadia Tasya Kusumawati, S.T.", "Nanas", "Community Leader", true],
    ["Dinar Rheina Rassendria, S.T.", null, "Vice Community Leader", true],
    ["Shania Chairunnisa Santoso, S.Kom.", null, "Treasurer", false],
    ["Wulan Sari", null, "Secretary", false],
    ["Rafi Amanu Surya Laksana", null, "Media Creative", false],
    ["Fira Novi Puspita Saputri", null, "Program Strategy", false],
  ] as const
).map(([name, nickname, role, is_founder], i) => ({
  id: i + 1,
  name,
  nickname,
  role,
  responsibilities: null,
  photo: null,
  is_founder,
  instagram: null,
  linkedin: null,
}));

const testimonials: Testimonial[] = [
  {
    id: 1,
    name: "Muhammad Zidan Atsal",
    institution: null,
    activity_name: "Volunteer NamaQu",
    photo: "/static/testimonials/zidan.jpg",
    message:
      "Beberapa kali ikut program-program NamaQu seru banget, beda dari volunteering-volunteering biasanya, bener-bener bisa aplikasiin ilmu yang kita punya ke program-program di NamaQu, dan tiap pertemuan selalu ada inovasi-inovasi program yang aku bahkan ga pernah kepikiran sebelumnya. Pesannya semoga NamaQu bisa terus memberikan manfaat ke anak-anak di kemvil yaa dan membantu mereka jadi orang-orang hebat di masa depan! Sukses selalu, NamaQu!",
  },
  {
    id: 2,
    name: "Ryu Rafli Ananta",
    institution: null,
    activity_name: "Volunteer NamaQu",
    photo: "/static/testimonials/ryu.jpg",
    message:
      "Selamaa ikut kegiatan NamaQu pastii selaluu senang karna kegiatann yang dibuatt samaa temen-temen seru dan bermanfaat bangett buat adek-adeknyaa. Pesan akuu smogaa dari kitaa temen-temen NamaQu maupun eksternal bisaa selalu bantuu dan sharing ilmu ataupun pengalaman kita yang bermanfaat buat adek-adeknyaa.",
  },
  {
    id: 3,
    name: "Insani Kamil Hamdallah",
    institution: null,
    activity_name: "Volunteer NamaQu",
    photo: "/static/testimonials/insani.jpg",
    message:
      "Bersyukur pernah jadi bagian dari komunitas ini, punya metode pendekatan penuh kasih dan inovatif yang ga bikin anak-anak bosan. Lancar selalu dan panjang umur, NamaQu.",
  },
];

const site: Site = {
  tagline: "Sebut Namamu, Gapai Cita-Citamu!",
  hero_title: "Belajar bareng, tumbuh bareng.",
  hero_subtitle:
    "NamaQu adalah komunitas sosial & pendidikan di Keputih, Surabaya, tempat anak-anak SD belajar bersama kakak-kakak volunteer setiap hari Minggu.",
  about:
    "<p>NamaQu merupakan komunitas sosial dan pendidikan yang berfokus pada kegiatan belajar bersama anak-anak SD. Kegiatan dilaksanakan di basecamp Keputih dengan melibatkan volunteer yang direkrut secara terbuka sesuai kebutuhan kegiatan.</p><p>Komunitas ini berawal dari ide Nadia Tasya (Nanas) dan Reyna, berbekal pengalaman mengelola event dan project kampus.</p>",
  basecamp_address: "Keputih, Sukolilo, Surabaya",
  maps_url: null,
  schedule_text: "Setiap hari Minggu",
  instagram: "namaqu.id",
  tiktok: "namaqu.id",
  linkedin: "https://www.linkedin.com/company/komunitas-namaqu/",
  email: null,
  contacts: [{ name: "Kak Ulen", purpose: "Narahubung", whatsapp: "6285171539202" }],
  collab_intro:
    "NamaQu terbuka untuk berkolaborasi dengan instansi, organisasi, komunitas, maupun pihak lain yang ingin ikut mendukung pendidikan anak-anak SD di Keputih. Berikut ketentuan kerja sama (partnership) dengan Komunitas NamaQu.",
  collab_terms: collabTerms,
  collab_contact: { name: "Kak Ulen", whatsapp: "6285171539202" },
  stats: { activities: activities.length, modules: modules.length, volunteers: 0 },
};

/** Meniru endpoint API Laravel (path yang sama) supaya halaman tidak perlu tahu sumber datanya. */
export function staticGet(path: string): unknown {
  const [pathname] = path.split("?");
  const parts = pathname.split("/").filter(Boolean).map(decodeURIComponent);

  switch (parts[0]) {
    case "site":
      return site;
    case "recruitments":
      return parts[1] ? (recruitments.find((r) => r.slug === parts[1]) ?? null) : recruitments;
    case "modules":
      return parts[1] ? (modules.find((m) => m.slug === parts[1]) ?? null) : modules;
    case "activities":
      return parts[1] ? (activities.find((a) => a.slug === parts[1]) ?? null) : activities;
    case "team":
      return team;
    case "testimonials":
      return testimonials;
    default:
      return null;
  }
}
