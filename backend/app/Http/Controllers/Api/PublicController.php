<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\LearningModule;
use App\Models\Recruitment;
use App\Models\SiteSetting;
use App\Models\TeamMember;
use App\Models\Testimonial;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * API publik (read-only) yang dikonsumsi frontend Next.js.
 * Semua respons boleh di-cache CDN/Vercel beberapa menit.
 */
class PublicController extends Controller
{
    public function site(): JsonResponse
    {
        $s = SiteSetting::allAsArray();

        $s['about'] = $this->html($s['about'] ?? null);
        $s['collab_terms'] = collect($s['collab_terms'] ?? [])->map(fn ($t) => [
            'title' => $t['title'] ?? '',
            'optional' => (bool) ($t['optional'] ?? false),
            'content' => $this->html($t['content'] ?? null),
            'note' => $t['note'] ?? null,
        ])->values();

        $s['stats'] = [
            'activities' => Activity::published()->count(),
            'modules' => LearningModule::published()->count(),
            'volunteers' => \App\Models\VolunteerRegistration::where('status', 'accepted')->count(),
        ];

        return $this->ok($s);
    }

    public function recruitments(Request $request): JsonResponse
    {
        $items = Recruitment::published()
            ->when($request->query('type'), fn ($q, $t) => $q->where('type', $t))
            ->orderByRaw('activity_date IS NULL, activity_date DESC')
            ->limit(50)
            ->get()
            ->map(fn ($r) => $this->recruitmentSummary($r));

        if ($request->query('status') === 'open') {
            $items = $items->where('is_open', true)->values();
        }

        return $this->ok($items);
    }

    public function recruitment(string $slug): JsonResponse
    {
        $r = Recruitment::published()->where('slug', $slug)->firstOrFail();

        return $this->ok($this->recruitmentSummary($r) + ['description' => $this->html($r->description)]);
    }

    private function recruitmentSummary(Recruitment $r): array
    {
            return [
                'id' => $r->id,
                'slug' => $r->slug,
                'type' => $r->type,
                'type_label' => Recruitment::TYPES[$r->type] ?? $r->type,
                'title' => $r->title,
                'summary' => $r->summary,
                'requirements' => $r->requirements ?? [],
                'activity_date' => $r->activity_date?->toDateString(),
                'activity_time' => $r->activity_time,
                'location' => $r->location,
                'registration_opens_at' => $r->registration_opens_at?->toDateString(),
                'registration_closes_at' => $r->registration_closes_at?->toDateString(),
                'quota' => $r->quota,
                'registration_method' => $r->registration_method,
                'external_url' => $r->external_url,
                'poster' => $this->file($r->poster),
                'is_open' => $r->isOpen(),
            ];
    }

    public function modules(Request $request): JsonResponse
    {
        $items = LearningModule::published()
            ->when($request->query('level'), fn ($q, $v) => $q->where('level', $v))
            ->when($request->query('category'), fn ($q, $v) => $q->where('category', $v))
            ->orderBy('sort_order')->orderBy('title')
            ->get()
            ->map(fn ($m) => $this->moduleSummary($m));

        return $this->ok($items, [
            'levels' => LearningModule::LEVELS,
            'categories' => LearningModule::CATEGORIES,
        ]);
    }

    public function module(string $slug): JsonResponse
    {
        $m = LearningModule::published()->where('slug', $slug)->firstOrFail();

        return $this->ok($this->moduleSummary($m) + [
            'content' => $this->html($m->content),
            'gallery' => collect($m->gallery ?? [])->map(fn ($f) => $this->file($f))->values(),
        ]);
    }

    public function activities(Request $request): JsonResponse
    {
        $page = Activity::published()
            ->when($request->query('type'), fn ($q, $v) => $q->where('type', $v))
            ->when($request->query('year'), fn ($q, $v) => $q->whereYear('held_on', (int) $v))
            ->orderByDesc('held_on')
            ->paginate(min((int) $request->query('per_page', 12), 48));

        $years = Activity::published()->pluck('held_on')->map->year->unique()->sortDesc()->values();

        return $this->ok(
            collect($page->items())->map(fn ($a) => $this->activitySummary($a)),
            [
                'years' => $years,
                'page' => $page->currentPage(),
                'last_page' => $page->lastPage(),
                'total' => $page->total(),
            ],
        );
    }

    public function activity(string $slug): JsonResponse
    {
        $a = Activity::published()->where('slug', $slug)->firstOrFail();

        return $this->ok($this->activitySummary($a) + [
            'description' => $this->html($a->description),
            'photos' => collect($a->photos ?? [])->map(fn ($f) => $this->file($f))->values(),
            'videos' => collect($a->videos ?? [])->map(fn ($v) => [
                'title' => $v['title'] ?? null,
                'url' => $v['url'] ?? null,
            ])->values(),
        ]);
    }

    public function team(): JsonResponse
    {
        return $this->ok(TeamMember::published()->orderByDesc('is_founder')->orderBy('sort_order')->get()->map(fn ($t) => [
            'id' => $t->id,
            'name' => $t->name,
            'nickname' => $t->nickname,
            'role' => $t->role,
            'responsibilities' => $t->responsibilities,
            'photo' => $this->file($t->photo),
            'is_founder' => $t->is_founder,
            'instagram' => $t->instagram,
            'linkedin' => $t->linkedin,
        ]));
    }

    public function testimonials(): JsonResponse
    {
        return $this->ok(Testimonial::published()->latest()->limit(60)->get()->map(fn ($t) => [
            'id' => $t->id,
            'name' => $t->display_name,
            'institution' => $t->institution,
            'activity_name' => $t->activity_name,
            'message' => $t->message,
            'photo' => $this->file($t->photo),
        ]));
    }

    // ---------------------------------------------------------------- helpers

    private function moduleSummary(LearningModule $m): array
    {
        return [
            'id' => $m->id,
            'slug' => $m->slug,
            'title' => $m->title,
            'summary' => $m->summary,
            'level' => $m->level,
            'level_label' => LearningModule::LEVELS[$m->level] ?? $m->level,
            'category' => $m->category,
            'category_label' => LearningModule::CATEGORIES[$m->category] ?? $m->category,
            'difficulty' => $m->difficulty,
            'difficulty_label' => LearningModule::DIFFICULTIES[$m->difficulty] ?? null,
            'target' => $m->target,
            'objectives' => $m->objectives ?? [],
            'external_url' => $m->external_url,
            'cover' => $this->file($m->cover),
        ];
    }

    private function activitySummary(Activity $a): array
    {
        return [
            'id' => $a->id,
            'slug' => $a->slug,
            'title' => $a->title,
            'type' => $a->type,
            'type_label' => Activity::TYPES[$a->type] ?? $a->type,
            'held_on' => $a->held_on?->toDateString(),
            'summary' => $a->summary,
            'cover' => $this->file($a->cover ?? ($a->photos[0] ?? null)),
            'photo_count' => count($a->photos ?? []),
        ];
    }

    private function file(?string $path): ?string
    {
        return $path ? Storage::disk('public')->url($path) : null;
    }

    private function html(?string $html): ?string
    {
        return filled($html) ? Str::sanitizeHtml($html) : null;
    }

    private function ok(mixed $data, array $meta = []): JsonResponse
    {
        return response()
            ->json(['data' => $data] + ($meta ? ['meta' => $meta] : []))
            ->header('Cache-Control', 'public, max-age=60, s-maxage=300, stale-while-revalidate=86400');
    }
}
