<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recruitment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RegistrationController extends Controller
{
    public function store(Request $request, string $slug): JsonResponse
    {
        $recruitment = Recruitment::published()->where('slug', $slug)->firstOrFail();

        if ($recruitment->registration_method !== 'form') {
            return response()->json(['message' => 'Pendaftaran kegiatan ini tidak melalui formulir website.'], 422);
        }

        if (! $recruitment->isOpen()) {
            return response()->json(['message' => 'Pendaftaran untuk kegiatan ini sudah ditutup.'], 422);
        }

        // honeypot anti-bot: field "website" harus kosong
        if (filled($request->input('website'))) {
            return response()->json(['message' => 'Terima kasih!'], 201);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => [
                'required', 'email', 'max:160',
                Rule::unique('volunteer_registrations')->where('recruitment_id', $recruitment->id),
            ],
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+\-\s]{9,20}$/'],
            'institution' => ['nullable', 'string', 'max:160'],
            'instagram' => ['nullable', 'string', 'max:60'],
            'motivation' => ['nullable', 'string', 'max:1500'],
        ], [
            'email.unique' => 'Email ini sudah terdaftar untuk kegiatan ini.',
            'phone.regex' => 'Nomor WhatsApp tidak valid.',
        ]);

        $data['instagram'] = isset($data['instagram']) ? ltrim($data['instagram'], '@') : null;

        $recruitment->registrations()->create($data);

        return response()->json(['message' => 'Pendaftaran berhasil! Pengurus akan menghubungi kamu lewat WhatsApp.'], 201);
    }
}
