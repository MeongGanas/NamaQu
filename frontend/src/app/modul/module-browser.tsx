"use client";

import { useEffect, useMemo, useState } from "react";
import { ModuleCard } from "@/components/cards";
import { EmptyState } from "@/components/decor";
import type { LearningModule } from "@/lib/shared";

const LEVELS = [
  { id: "all", label: "Semua tingkat" },
  { id: "kecil", label: "Tingkat Kecil" },
  { id: "sedang", label: "Tingkat Sedang" },
  { id: "besar", label: "Tingkat Besar" },
] as const;

const CATEGORIES = [
  { id: "all", label: "Semua materi" },
  { id: "calistung", label: "✏️ Calistung" },
  { id: "logika_matematika", label: "🧩 Logika & Matematika" },
  { id: "game_coding", label: "🎮 Game Coding" },
] as const;

function Pills<T extends string>({
  options,
  value,
  onChange,
  label,
}: {
  options: readonly { id: T; label: string }[];
  value: T;
  onChange: (v: T) => void;
  label: string;
}) {
  return (
    <div role="group" aria-label={label} className="flex flex-wrap gap-2">
      {options.map((o) => (
        <button
          key={o.id}
          type="button"
          onClick={() => onChange(o.id)}
          aria-pressed={value === o.id}
          className={`rounded-full border-2 border-ink px-4 py-1.5 text-sm font-semibold transition ${
            value === o.id ? "bg-ink text-paper" : "bg-white hover:bg-coral-soft"
          }`}
        >
          {o.label}
        </button>
      ))}
    </div>
  );
}

export function ModuleBrowser({ modules }: { modules: LearningModule[] }) {
  const [level, setLevel] = useState<(typeof LEVELS)[number]["id"]>("all");
  const [category, setCategory] = useState<(typeof CATEGORIES)[number]["id"]>("all");

  // dukung link dari beranda, mis. /modul#calistung
  useEffect(() => {
    const hash = window.location.hash.slice(1);
    const match = CATEGORIES.find((c) => c.id === hash);
    // eslint-disable-next-line react-hooks/set-state-in-effect -- sinkron sekali dari URL hash
    if (match) setCategory(match.id);
  }, []);

  const filtered = useMemo(
    () =>
      modules.filter((m) => (level === "all" || m.level === level) && (category === "all" || m.category === category)),
    [modules, level, category],
  );

  return (
    <div>
      <div className="card mb-10 space-y-3 bg-white/80 p-4">
        <Pills options={LEVELS} value={level} onChange={setLevel} label="Filter tingkat" />
        <Pills options={CATEGORIES} value={category} onChange={setCategory} label="Filter jenis materi" />
      </div>
      {filtered.length ? (
        <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          {filtered.map((m, i) => (
            <ModuleCard key={m.id} m={m} i={i} />
          ))}
        </div>
      ) : (
        <EmptyState>Belum ada modul untuk pilihan ini.</EmptyState>
      )}
    </div>
  );
}
