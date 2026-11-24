import Image from "next/image";
import Link from "next/link";
import { formatDate, type Activity, type LearningModule, type Recruitment, type TeamMember, type Testimonial } from "@/lib/shared";
import { Heart, Sparkle, Star } from "./decor";

const LEVEL_TONE: Record<LearningModule["level"], string> = {
  kecil: "bg-tangerine",
  sedang: "bg-peri",
  besar: "bg-lilac",
};

const CATEGORY_ICON: Record<LearningModule["category"], string> = {
  calistung: "✏️",
  logika_matematika: "🧩",
  bahasa_inggris: "🔤",
  sains_teknologi: "🔬",
  game_coding: "🎮",
};

const TILTS = ["-rotate-2", "rotate-1", "-rotate-1", "rotate-2"];

function Placeholder({ label, tone = "bg-coral-soft" }: { label: string; tone?: string }) {
  return (
    <div className={`flex h-full w-full items-center justify-center ${tone}`}>
      <span className="font-hand text-3xl text-ink/60">{label}</span>
    </div>
  );
}

export function RecruitmentCard({ r }: { r: Recruitment }) {
  const isEvent = r.type === "event";
  return (
    <Link href={`/volunteer/${r.slug}`} className="card group flex flex-col overflow-hidden transition-transform hover:-translate-y-1">
      <div className={`flex items-center justify-between border-b-2 border-ink px-5 py-3 ${isEvent ? "bg-grape text-white" : "bg-tangerine"}`}>
        <span className="text-sm font-bold uppercase tracking-wider">{r.type_label}</span>
        <span className={`chip ${r.is_open ? "bg-sun text-ink" : "bg-white text-ink-soft"}`}>
          {r.is_open ? "Dibuka" : "Ditutup"}
        </span>
      </div>
      <div className="flex flex-1 flex-col p-5">
        <h3 className="font-display text-2xl font-semibold leading-tight group-hover:text-grape">{r.title}</h3>
        {r.summary && <p className="mt-2 text-ink-soft">{r.summary}</p>}
        <dl className="mt-4 grid gap-1.5 text-sm">
          {r.activity_date && (
            <div className="flex gap-2">
              <dt className="w-24 shrink-0 font-semibold">Kegiatan</dt>
              <dd>
                {formatDate(r.activity_date, { weekday: "long" })}
                {r.activity_time ? `, ${r.activity_time}` : ""}
              </dd>
            </div>
          )}
          {r.registration_closes_at && (
            <div className="flex gap-2">
              <dt className="w-24 shrink-0 font-semibold">Batas daftar</dt>
              <dd>{formatDate(r.registration_closes_at)}</dd>
            </div>
          )}
          {r.quota && (
            <div className="flex gap-2">
              <dt className="w-24 shrink-0 font-semibold">Kuota</dt>
              <dd>{r.quota} orang</dd>
            </div>
          )}
        </dl>
        <span className="mt-auto pt-5 font-semibold text-grape">Lihat detail & cara daftar →</span>
      </div>
    </Link>
  );
}

export function ModuleCard({ m, i = 0 }: { m: LearningModule; i?: number }) {
  return (
    <Link href={`/modul/${m.slug}`} className="card group flex flex-col overflow-hidden transition-transform hover:-translate-y-1">
      <div className={`relative aspect-[4/3] border-b-2 border-ink ${LEVEL_TONE[m.level]}`}>
        {m.cover ? (
          <Image src={m.cover} alt="" fill sizes="(min-width: 1024px) 33vw, 100vw" className="object-cover" />
        ) : (
          <div className="flex h-full items-center justify-center text-6xl">
            <span className={`${TILTS[i % TILTS.length]} inline-block`}>{CATEGORY_ICON[m.category]}</span>
          </div>
        )}
        <span className="chip absolute left-3 top-3 bg-white">{m.level_label}</span>
      </div>
      <div className="flex flex-1 flex-col p-5">
        <p className="text-sm font-semibold text-grape">
          {CATEGORY_ICON[m.category]} {m.category_label}
        </p>
        <h3 className="mt-1 font-display text-xl font-semibold leading-snug group-hover:text-grape">{m.title}</h3>
        {m.summary && <p className="mt-2 line-clamp-2 text-sm text-ink-soft">{m.summary}</p>}
        <div className="mt-auto flex flex-wrap items-center gap-2 pt-4 text-xs">
          <Difficulty value={m.difficulty} label={m.difficulty_label} />
          {m.target && <span className="text-ink-soft">· {m.target}</span>}
        </div>
      </div>
    </Link>
  );
}

export function Difficulty({ value, label }: { value: number; label: string | null }) {
  return (
    <span className="inline-flex items-center gap-1 font-semibold" aria-label={`Kesulitan: ${label ?? value}`}>
      {[1, 2, 3].map((n) => (
        <Star key={n} className="h-4 w-4" color={n <= value ? "var(--color-sun)" : "white"} />
      ))}
      <span className="ml-1">{label}</span>
    </span>
  );
}

export function ActivityCard({ a, i = 0 }: { a: Activity; i?: number }) {
  return (
    <Link
      href={`/dokumentasi/${a.slug}`}
      className={`group block rounded-sm border-2 border-ink bg-white p-3 pb-4 shadow-[4px_4px_0_0_var(--color-ink)] transition-transform hover:rotate-0 hover:-translate-y-1 ${TILTS[i % TILTS.length]}`}
    >
      <div className="relative aspect-square overflow-hidden border-2 border-ink">
        {a.cover ? (
          <Image src={a.cover} alt="" fill sizes="(min-width: 1024px) 25vw, 50vw" className="object-cover" />
        ) : (
          <Placeholder label={a.type === "event" ? "event!" : "minggu ceria"} tone={a.type === "event" ? "bg-coral" : "bg-peri"} />
        )}
        {a.photo_count > 0 && <span className="chip absolute bottom-2 right-2 bg-white">{a.photo_count} foto</span>}
      </div>
      <p className="mt-3 font-hand text-xl leading-tight text-grape">{formatDate(a.held_on)}</p>
      <h3 className="font-display text-lg font-semibold leading-snug group-hover:text-grape">{a.title}</h3>
    </Link>
  );
}

export function TestimonialCard({ t, i = 0 }: { t: Testimonial; i?: number }) {
  const tones = ["bg-coral-soft", "bg-tangerine-soft", "bg-peri-soft", "bg-sun/60"];
  return (
    <figure className={`card relative flex h-full flex-col p-6 ${tones[i % tones.length]}`}>
      <Heart className="absolute -right-3 -top-3 h-9 w-9 rotate-12" />
      <blockquote className="flex-1 text-[15px] leading-relaxed text-ink">“{t.message}”</blockquote>
      <figcaption className="mt-5 flex items-center gap-3">
        {t.photo ? (
          <Image src={t.photo} alt="" width={44} height={44} className="h-11 w-11 rounded-full border-2 border-ink object-cover" />
        ) : (
          <span className="flex h-11 w-11 items-center justify-center rounded-full border-2 border-ink bg-white font-display font-bold">
            {t.name.charAt(0)}
          </span>
        )}
        <span>
          <span className="block font-semibold">{t.name}</span>
          <span className="block text-sm text-ink-soft">
            {t.activity_name}
            {t.institution ? ` · ${t.institution}` : ""}
          </span>
        </span>
      </figcaption>
    </figure>
  );
}

export function TeamCard({ m, i = 0 }: { m: TeamMember; i?: number }) {
  const tones = ["bg-coral", "bg-tangerine", "bg-peri", "bg-lilac", "bg-sun"];
  return (
    <article className="card flex flex-col items-center p-6 text-center">
      <div className={`relative h-36 w-36 overflow-hidden rounded-full border-2 border-ink ${tones[i % tones.length]}`}>
        {m.photo ? (
          <Image src={m.photo} alt={`Foto ${m.name}`} fill sizes="144px" className="object-cover" />
        ) : (
          <span className="flex h-full items-center justify-center font-display text-5xl font-bold">{m.name.charAt(0)}</span>
        )}
      </div>
      {m.is_founder && (
        <span className="chip -mt-3 rotate-[-4deg] bg-grape text-white">
          <Sparkle className="h-3 w-3" color="white" /> Founder
        </span>
      )}
      <h3 className="mt-3 font-display text-xl font-semibold">
        {m.name}
        {m.nickname && <span className="font-hand text-2xl font-normal text-grape"> ({m.nickname})</span>}
      </h3>
      <p className="text-sm font-semibold text-ink-soft">{m.role}</p>
      {m.responsibilities && <p className="mt-3 text-sm text-ink-soft">{m.responsibilities}</p>}
      <div className="mt-4 flex gap-2 text-sm">
        {m.instagram && (
          <a className="chip bg-white hover:bg-coral-soft" href={`https://instagram.com/${m.instagram}`} target="_blank" rel="noreferrer">
            Instagram
          </a>
        )}
        {m.linkedin && (
          <a className="chip bg-white hover:bg-peri-soft" href={m.linkedin} target="_blank" rel="noreferrer">
            LinkedIn
          </a>
        )}
      </div>
    </article>
  );
}
