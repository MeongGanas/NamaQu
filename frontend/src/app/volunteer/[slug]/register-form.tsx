"use client";

import { useActionState } from "react";
import { register, type RegisterState } from "./actions";

const initial: RegisterState = { status: "idle" };

function Field({
  name,
  label,
  state,
  required,
  type = "text",
  placeholder,
  textarea,
  hint,
}: {
  name: string;
  label: string;
  state: RegisterState;
  required?: boolean;
  type?: string;
  placeholder?: string;
  textarea?: boolean;
  hint?: string;
}) {
  const error = state.errors?.[name]?.[0];
  const cls = `mt-1 w-full rounded-2xl border-2 bg-white px-4 py-2.5 outline-none transition focus:border-grape focus:ring-4 focus:ring-coral/50 ${
    error ? "border-grape" : "border-ink"
  }`;
  return (
    <label className="block">
      <span className="font-semibold">
        {label} {required && <span className="text-grape">*</span>}
      </span>
      {textarea ? (
        <textarea name={name} rows={4} className={cls} placeholder={placeholder} defaultValue={state.values?.[name]} />
      ) : (
        <input
          name={name}
          type={type}
          required={required}
          className={cls}
          placeholder={placeholder}
          defaultValue={state.values?.[name]}
          aria-invalid={!!error}
        />
      )}
      {hint && !error && <span className="mt-1 block text-sm text-ink-soft">{hint}</span>}
      {error && <span className="mt-1 block text-sm font-semibold text-grape">{error}</span>}
    </label>
  );
}

export function RegisterForm({ slug }: { slug: string }) {
  const [state, action, pending] = useActionState(register.bind(null, slug), initial);

  if (state.status === "success") {
    return (
      <div className="card bg-tangerine p-8 text-center" role="status">
        <p className="text-5xl">🎉</p>
        <h3 className="mt-3 font-display text-2xl font-semibold">Pendaftaran terkirim!</h3>
        <p className="mt-2 text-ink-soft">{state.message}</p>
      </div>
    );
  }

  return (
    <form action={action} className="card space-y-4 p-6 sm:p-8" noValidate={false}>
      <h3 className="font-display text-2xl font-semibold">Formulir pendaftaran</h3>
      <Field name="name" label="Nama lengkap" required state={state} />
      <div className="grid gap-4 sm:grid-cols-2">
        <Field name="email" label="Email" type="email" required state={state} />
        <Field name="phone" label="No. WhatsApp" type="tel" required placeholder="08xxxxxxxxxx" state={state} />
      </div>
      <div className="grid gap-4 sm:grid-cols-2">
        <Field name="institution" label="Kampus / instansi" state={state} />
        <Field name="instagram" label="Username Instagram" placeholder="@kamu" state={state} />
      </div>
      <Field name="motivation" label="Kenapa ingin jadi volunteer?" textarea state={state} />
      {/* honeypot anti-bot, disembunyikan dari manusia */}
      <input type="text" name="website" tabIndex={-1} autoComplete="off" className="hidden" aria-hidden />

      {state.status === "error" && state.message && (
        <p className="rounded-2xl border-2 border-grape bg-coral-soft px-4 py-3 font-semibold text-grape-dark" role="alert">
          {state.message}
        </p>
      )}

      <button type="submit" className="btn btn-primary w-full text-lg" disabled={pending}>
        {pending ? "Mengirim…" : "Kirim pendaftaran"}
      </button>
      <p className="text-center text-xs text-ink-soft">Data kamu hanya dipakai pengurus NamaQu untuk keperluan kegiatan ini.</p>
    </form>
  );
}
