"use server";

import { postRegistration } from "@/lib/api";

export type RegisterState = {
  status: "idle" | "success" | "error";
  message?: string;
  errors?: Record<string, string[]>;
  values?: Record<string, string>;
};

const FIELDS = ["name", "email", "phone", "institution", "instagram", "motivation", "website"] as const;

export async function register(slug: string, _prev: RegisterState, formData: FormData): Promise<RegisterState> {
  const values = Object.fromEntries(FIELDS.map((f) => [f, String(formData.get(f) ?? "").trim()]));

  try {
    const res = await postRegistration(slug, values);
    if (res.ok) {
      return { status: "success", message: res.message };
    }
    return {
      status: "error",
      message: res.errors ? "Periksa lagi isian yang ditandai, ya." : (res.message ?? "Pendaftaran gagal."),
      errors: res.errors,
      values,
    };
  } catch {
    return {
      status: "error",
      message: "Server NamaQu sedang tidak bisa dihubungi. Coba lagi beberapa saat lagi, atau hubungi CP kami.",
      values,
    };
  }
}
