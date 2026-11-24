<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Open recruitment volunteer (mingguan & kegiatan besar)
        Schema::create('recruitments', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20)->index(); // weekly | event
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('summary')->nullable();
            $table->longText('description')->nullable();
            $table->json('requirements')->nullable();   // daftar persyaratan
            $table->date('activity_date')->nullable();
            $table->string('activity_time')->nullable(); // "08.00 - 11.00 WIB"
            $table->string('location')->nullable();
            $table->date('registration_opens_at')->nullable();
            $table->date('registration_closes_at')->nullable();
            $table->unsignedInteger('quota')->nullable();
            $table->string('registration_method', 20)->default('form'); // form | instagram | external
            $table->string('external_url')->nullable(); // link IG story / google form
            $table->string('poster')->nullable();
            $table->boolean('is_published')->default(false)->index();
            $table->timestamps();
        });

        Schema::create('volunteer_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recruitment_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('phone', 30);
            $table->string('institution')->nullable(); // kampus / instansi
            $table->string('instagram')->nullable();
            $table->text('motivation')->nullable();
            $table->string('status', 20)->default('pending')->index(); // pending | accepted | rejected
            $table->text('admin_note')->nullable();
            $table->timestamps();
            $table->unique(['recruitment_id', 'email']);
        });

        // 2. Modul pembelajaran anak SD
        Schema::create('learning_modules', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('level', 20)->index();    // kecil | sedang | besar
            $table->string('category', 30)->index(); // calistung | logika_matematika | game_coding
            $table->text('summary')->nullable();
            $table->longText('content')->nullable();
            $table->string('target')->nullable();     // "Usia 6-7 tahun / belum lancar membaca"
            $table->unsignedTinyInteger('difficulty')->default(1); // 1-3
            $table->json('objectives')->nullable();   // tujuan pembelajaran
            $table->string('external_url')->nullable(); // link website game coding
            $table->string('cover')->nullable();
            $table->json('gallery')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(false)->index();
            $table->timestamps();
        });

        // 3. Dokumentasi kegiatan
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('type', 20)->index(); // weekly | event
            $table->date('held_on')->index();
            $table->text('summary')->nullable();
            $table->longText('description')->nullable();
            $table->string('cover')->nullable();
            $table->json('photos')->nullable();
            $table->json('videos')->nullable(); // [{title, url}] YouTube / Instagram
            $table->boolean('is_published')->default(false)->index();
            $table->timestamps();
        });

        // 4. Biodata pengurus
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('nickname')->nullable();
            $table->string('role');
            $table->text('responsibilities')->nullable();
            $table->string('photo')->nullable();
            $table->boolean('is_founder')->default(false);
            $table->string('instagram')->nullable();
            $table->string('linkedin')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true)->index();
            $table->timestamps();
        });

        // 5. Kesan & pesan volunteer
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('show_full_name')->default(true); // sesuai persetujuan volunteer
            $table->string('institution')->nullable();
            $table->string('activity_name');
            $table->foreignId('activity_id')->nullable()->constrained()->nullOnDelete();
            $table->text('message');
            $table->string('photo')->nullable();
            $table->boolean('is_published')->default(false)->index();
            $table->timestamps();
        });

        // 6 & 7. Kontak, media sosial, T&C kolaborasi, teks halaman
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('team_members');
        Schema::dropIfExists('activities');
        Schema::dropIfExists('learning_modules');
        Schema::dropIfExists('volunteer_registrations');
        Schema::dropIfExists('recruitments');
    }
};
