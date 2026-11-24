"use client";

import { useMemo, useState } from "react";
import { ActivityCard } from "@/components/cards";
import { EmptyState } from "@/components/decor";
import type { Activity } from "@/lib/shared";

export function ActivityBrowser({ activities }: { activities: Activity[] }) {
  const [type, setType] = useState<"all" | "weekly" | "event">("all");
  const [year, setYear] = useState<string>("all");

  const years = useMemo(
    () => [...new Set(activities.map((a) => a.held_on.slice(0, 4)))].sort().reverse(),
    [activities],
  );

  const groups = useMemo(() => {
    const list = activities.filter(
      (a) => (type === "all" || a.type === type) && (year === "all" || a.held_on.startsWith(year)),
    );
    // kelompokkan per bulan pelaksanaan
    const map = new Map<string, Activity[]>();
    for (const a of list) {
      const key = new Intl.DateTimeFormat("id-ID", { month: "long", year: "numeric", timeZone: "Asia/Jakarta" }).format(
        new Date(`${a.held_on}T00:00:00+07:00`),
      );
      map.set(key, [...(map.get(key) ?? []), a]);
    }
    return [...map.entries()];
  }, [activities, type, year]);

  const btn = (active: boolean) =>
    `rounded-full border-2 border-ink px-4 py-1.5 text-sm font-semibold transition ${
      active ? "bg-ink text-paper" : "bg-white hover:bg-coral-soft"
    }`;

  return (
    <div>
      <div className="card mb-10 flex flex-wrap items-center gap-2 bg-white/80 p-4">
        {(
          [
            ["all", "Semua kegiatan"],
            ["weekly", "Kegiatan mingguan"],
            ["event", "Event khusus"],
          ] as const
        ).map(([id, label]) => (
          <button key={id} type="button" className={btn(type === id)} aria-pressed={type === id} onClick={() => setType(id)}>
            {label}
          </button>
        ))}
        {years.length > 1 && (
          <label className="ml-auto flex items-center gap-2 text-sm font-semibold">
            Tahun
            <select
              value={year}
              onChange={(e) => setYear(e.target.value)}
              className="rounded-full border-2 border-ink bg-white px-3 py-1.5"
            >
              <option value="all">Semua</option>
              {years.map((y) => (
                <option key={y}>{y}</option>
              ))}
            </select>
          </label>
        )}
      </div>

      {groups.length ? (
        <div className="space-y-14">
          {groups.map(([month, items]) => (
            <section key={month}>
              <h2 className="mb-6 font-hand text-4xl text-grape">{month}</h2>
              <div className="grid grid-cols-2 gap-6 lg:grid-cols-4">
                {items.map((a, i) => (
                  <ActivityCard key={a.id} a={a} i={i} />
                ))}
              </div>
            </section>
          ))}
        </div>
      ) : (
        <EmptyState>Belum ada dokumentasi untuk pilihan ini.</EmptyState>
      )}
    </div>
  );
}
