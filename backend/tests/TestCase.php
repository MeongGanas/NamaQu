<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    /**
     * Pengaman: test memakai RefreshDatabase (menghapus semua tabel).
     * Tolak berjalan kalau ternyata terhubung ke database sungguhan (MariaDB dev/produksi).
     */
    protected function beforeRefreshingDatabase()
    {
        $connection = config('database.default');
        $database = config("database.connections.{$connection}.database");

        if ($connection !== 'sqlite' || $database !== ':memory:') {
            throw new RuntimeException("Test dibatalkan: database '{$connection}:{$database}' bukan SQLite in-memory.");
        }
    }
}
