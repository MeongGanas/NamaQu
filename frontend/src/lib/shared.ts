/** Tipe data API & helper format: aman dipakai di Server maupun Client Component. */

export type Recruitment = {
  id: number;
  slug: string;
  type: "weekly" | "event";
  type_label: string;
  title: string;
  summary: string | null;
  description?: string | null;
  requirements: string[];
  activity_date: string | null;
  activity_time: string | null;
  location: string | null;
  registration_opens_at: string | null;
  registration_closes_at: string | null;
  quota: number | null;
  registration_method: "form" | "instagram" | "external";
  external_url: string | null;
  poster: string | null;
  is_open: boolean;
};

export type LearningModule = {
  id: number;
  slug: string;
  title: string;
  summary: string | null;
  level: "kecil" | "sedang" | "besar";
  level_label: string;
  category: "calistung" | "logika_matematika" | "game_coding";
  category_label: string;
  difficulty: number;
  difficulty_label: string | null;
  target: string | null;
  objectives: string[];
  external_url: string | null;
  cover: string | null;
  content?: string | null;
  gallery?: string[];
};

export type Activity = {
  id: number;
  slug: string;
  title: string;
  type: "weekly" | "event";
  type_label: string;
  held_on: string;
  summary: string | null;
  cover: string | null;
  photo_count: number;
  description?: string | null;
  photos?: string[];
  videos?: { title: string | null; url: string }[];
  album_url?: string | null;
};

export type TeamMember = {
  id: number;
  name: string;
  nickname: string | null;
  role: string;
  responsibilities: string | null;
  photo: string | null;
  is_founder: boolean;
  instagram: string | null;
  linkedin: string | null;
};

export type Testimonial = {
  id: number;
  name: string;
  institution: string | null;
  activity_name: string;
  message: string;
  photo: string | null;
};

export type Contact = { name: string; purpose?: string | null; whatsapp: string };

export type CollabTerm = {
  title: string;
  optional: boolean;
  content: string | null;
  note: string | null;
};

export type Site = {
  tagline: string | null;
  hero_title: string | null;
  hero_subtitle: string | null;
  about: string | null;
  basecamp_address: string | null;
  maps_url: string | null;
  schedule_text: string | null;
  instagram: string | null;
  tiktok: string | null;
  linkedin: string | null;
  email: string | null;
  contacts: Contact[] | null;
  collab_intro: string | null;
  collab_terms: CollabTerm[];
  collab_contact: { name?: string | null; whatsapp?: string | null } | null;
  stats: { activities: number; modules: number; volunteers: number };
};

// ------------------------------------------------------------------ helpers

export function formatDate(iso: string | null | undefined, opts: Intl.DateTimeFormatOptions = {}) {
  if (!iso) return null;
  return new Intl.DateTimeFormat("id-ID", {
    day: "numeric",
    month: "long",
    year: "numeric",
    timeZone: "Asia/Jakarta",
    ...opts,
  }).format(new Date(`${iso}T00:00:00+07:00`));
}

export function waLink(phone: string | null | undefined, text?: string) {
  if (!phone) return null;
  const digits = phone.replace(/\D/g, "").replace(/^0/, "62");
  return `https://wa.me/${digits}${text ? `?text=${encodeURIComponent(text)}` : ""}`;
}

export function igLink(username: string | null | undefined) {
  return username ? `https://www.instagram.com/${username.replace(/^@/, "")}/` : null;
}

export function tiktokLink(username: string | null | undefined) {
  return username ? `https://www.tiktok.com/@${username.replace(/^@/, "")}` : null;
}
