<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            // album lengkap di Google Drive / Google Photos, supaya foto yang di-upload ke server cukup yang terbaik saja
            $table->string('album_url')->nullable()->after('videos');
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn('album_url');
        });
    }
};
