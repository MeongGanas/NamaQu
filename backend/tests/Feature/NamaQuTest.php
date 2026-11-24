<?php

namespace Tests\Feature;

use App\Models\Recruitment;
use App\Models\User;
use App\Models\VolunteerRegistration;
use Database\Seeders\DemoSeeder;
use Database\Seeders\SiteSettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class NamaQuTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        \Illuminate\Support\Facades\Storage::fake('public');
        $this->seed([SiteSettingSeeder::class, \Database\Seeders\NamaQuContentSeeder::class, DemoSeeder::class]);
    }

    public static function apiEndpoints(): array
    {
        return [
            ['/api/site'], ['/api/recruitments'], ['/api/recruitments?status=open'],
            ['/api/recruitments/volunteer-hari-anak'], ['/api/modules'], ['/api/modules?level=kecil'],
            ['/api/activities'], ['/api/team'], ['/api/testimonials'],
        ];
    }

    #[DataProvider('apiEndpoints')]
    public function test_public_api_endpoints_respond(string $url): void
    {
        $this->getJson($url)->assertOk()->assertJsonStructure(['data']);
    }

    public function test_unpublished_and_unknown_items_are_hidden(): void
    {
        Recruitment::where('slug', 'volunteer-hari-anak')->update(['is_published' => false]);

        $this->getJson('/api/recruitments/volunteer-hari-anak')->assertNotFound();
        $this->getJson('/api/modules/tidak-ada')->assertNotFound();
    }

    public function test_volunteer_can_register_through_form(): void
    {
        $this->postJson('/api/recruitments/volunteer-hari-anak/register', [
            'name' => 'Sari', 'email' => 'sari@example.com', 'phone' => '081234567890', 'instagram' => '@sari',
        ])->assertCreated();

        $this->assertDatabaseHas('volunteer_registrations', ['email' => 'sari@example.com', 'instagram' => 'sari', 'status' => 'pending']);

        // email yang sama tidak bisa daftar dua kali
        $this->postJson('/api/recruitments/volunteer-hari-anak/register', [
            'name' => 'Sari', 'email' => 'sari@example.com', 'phone' => '081234567890',
        ])->assertUnprocessable()->assertJsonValidationErrors('email');
    }

    public function test_registration_rejected_when_closed_or_not_form_based(): void
    {
        // volunteer mingguan daftar lewat Instagram, bukan form
        $this->postJson('/api/recruitments/volunteer-minggu-ini/register', [
            'name' => 'A', 'email' => 'a@example.com', 'phone' => '081234567890',
        ])->assertUnprocessable();

        Recruitment::where('slug', 'volunteer-hari-anak')->update(['registration_closes_at' => now()->subDay()]);
        $this->postJson('/api/recruitments/volunteer-hari-anak/register', [
            'name' => 'A', 'email' => 'a@example.com', 'phone' => '081234567890',
        ])->assertUnprocessable();
    }

    public function test_honeypot_silently_drops_bots(): void
    {
        $this->postJson('/api/recruitments/volunteer-hari-anak/register', [
            'name' => 'Bot', 'email' => 'bot@example.com', 'phone' => '081234567890', 'website' => 'http://spam',
        ])->assertCreated();

        $this->assertDatabaseMissing('volunteer_registrations', ['email' => 'bot@example.com']);
    }

    public function test_testimonial_respects_name_consent(): void
    {
        $names = collect($this->getJson('/api/testimonials')->json('data'))->pluck('name');

        $this->assertContains('Rina A.', $names);
        $this->assertNotContains('Rina Amelia', $names);
        $this->assertContains('Muhammad Zidan Atsal', $names);
    }

    public function test_real_content_is_seeded(): void
    {
        $this->getJson('/api/team')->assertJsonCount(6, 'data')
            ->assertJsonPath('data.0.role', 'Community Leader');
        $this->getJson('/api/site')->assertJsonPath('data.instagram', 'namaqu.id')
            ->assertJsonPath('data.tiktok', 'namaqu.id')
            ->assertJsonCount(9, 'data.collab_terms')
            ->assertJsonPath('data.collab_terms.5.optional', true)
            ->assertJsonPath('data.tagline', 'Sebut Namamu, Gapai Cita-Citamu!');
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists('testimonials/zidan.jpg');
    }

    public static function adminPages(): array
    {
        return [
            ['/admin'],
            ['/admin/recruitments'], ['/admin/recruitments/create'],
            ['/admin/volunteer-registrations'],
            ['/admin/learning-modules'], ['/admin/learning-modules/create'],
            ['/admin/activities'], ['/admin/activities/create'],
            ['/admin/team-members'], ['/admin/team-members/create'],
            ['/admin/testimonials'], ['/admin/testimonials/create'],
            ['/admin/pengaturan-situs'],
        ];
    }

    #[DataProvider('adminPages')]
    public function test_admin_pages_render(string $url): void
    {
        $this->actingAs(User::first())->get($url)->assertOk();
    }

    public function test_admin_edit_pages_render(): void
    {
        $user = User::first();
        VolunteerRegistration::create([
            'recruitment_id' => Recruitment::first()->id, 'name' => 'X', 'email' => 'x@example.com', 'phone' => '0812',
        ]);

        $resources = [
            'recruitments' => Recruitment::class,
            'volunteer-registrations' => VolunteerRegistration::class,
            'learning-modules' => \App\Models\LearningModule::class,
            'activities' => \App\Models\Activity::class,
            'team-members' => \App\Models\TeamMember::class,
            'testimonials' => \App\Models\Testimonial::class,
        ];

        foreach ($resources as $slug => $model) {
            $id = $model::query()->value('id');
            $this->actingAs($user)->get("/admin/{$slug}/{$id}/edit")->assertOk();
        }
    }

    public function test_site_settings_page_saves(): void
    {
        $this->actingAs(User::first());

        \Livewire\Livewire::test(\App\Filament\Pages\SiteSettings::class)
            ->set('data.instagram', 'namaqu.sby')
            ->set('data.hero_title', 'Judul baru')
            ->call('save')
            ->assertHasNoErrors();

        $this->getJson('/api/site')
            ->assertJsonPath('data.instagram', 'namaqu.sby')
            ->assertJsonPath('data.hero_title', 'Judul baru');
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin/recruitments')->assertRedirect('/admin/login');
    }
}
