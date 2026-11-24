<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * @property-read Schema $form
 */
class SiteSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Profil, Kontak & Kolaborasi';

    protected static ?string $title = 'Profil, Kontak & Kolaborasi';

    protected static ?string $slug = 'pengaturan-situs';

    /** Field yang disimpan di tabel site_settings. */
    public const KEYS = [
        'tagline', 'hero_title', 'hero_subtitle', 'about', 'basecamp_address', 'maps_url', 'schedule_text',
        'vision', 'missions', 'programs', 'impact', 'milestones',
        'instagram', 'tiktok', 'linkedin', 'email', 'contacts',
        'collab_intro', 'collab_terms', 'collab_contact', 'collab_partners',
    ];

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(SiteSetting::allAsArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema->statePath('data')->components([
            Tabs::make()->persistTabInQueryString()->tabs([
                Tab::make('Profil')->icon(Heroicon::OutlinedHome)->schema([
                    TextInput::make('tagline')->label('Tagline')->placeholder('Sebut Namamu, Gapai Cita-Citamu!'),
                    TextInput::make('hero_title')->label('Judul besar beranda')->placeholder('Belajar bareng, tumbuh bareng.'),
                    Textarea::make('hero_subtitle')->label('Sub-judul beranda')->rows(2),
                    RichEditor::make('about')->label('Tentang NamaQu'),
                    TextInput::make('basecamp_address')->label('Alamat basecamp'),
                    TextInput::make('maps_url')->label('Link Google Maps')->url(),
                    TextInput::make('schedule_text')->label('Jadwal rutin')->placeholder('Setiap Minggu, 08.00 - 11.00 WIB'),
                ]),
                Tab::make('Visi, Misi & Program')->icon(Heroicon::OutlinedSparkles)->schema([
                    Textarea::make('vision')->label('Visi')->rows(3),
                    Repeater::make('missions')->label('Misi')->defaultItems(0)->reorderable()
                        ->addActionLabel('Tambah misi')
                        ->simple(Textarea::make('text')->rows(2)->required()),
                    Repeater::make('programs')->label('Program unggulan')->defaultItems(0)->reorderable()->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['code'] ?? null)
                        ->addActionLabel('Tambah program')
                        ->schema([
                            TextInput::make('code')->label('Kode')->placeholder('QULEARN')->required(),
                            TextInput::make('name')->label('Nama lengkap')->placeholder('NamaQu Learning'),
                            Textarea::make('description')->label('Penjelasan')->rows(3)->columnSpanFull(),
                        ])->columns(2),
                    Repeater::make('impact')->label('Angka dampak (tampil di beranda & tentang)')->defaultItems(0)->reorderable()
                        ->addActionLabel('Tambah angka')
                        ->schema([
                            TextInput::make('value')->label('Angka')->placeholder('30–40')->required(),
                            TextInput::make('label')->label('Keterangan')->placeholder('anak didampingi')->required(),
                        ])->columns(2),
                    Repeater::make('milestones')->label('Perjalanan (linimasa)')->defaultItems(0)->reorderable()
                        ->addActionLabel('Tambah momen')
                        ->schema([
                            TextInput::make('date')->label('Waktu')->placeholder('8 Agustus 2025')->required(),
                            TextInput::make('title')->label('Momen')->required(),
                        ])->columns(2),
                ]),
                Tab::make('Kontak & Media Sosial')->icon(Heroicon::OutlinedPhone)->schema([
                    TextInput::make('instagram')->label('Instagram (username)')->prefix('@'),
                    TextInput::make('tiktok')->label('TikTok (username)')->prefix('@'),
                    TextInput::make('linkedin')->label('LinkedIn (URL)')->url(),
                    TextInput::make('email')->label('Email')->email(),
                    Repeater::make('contacts')->label('Contact Person')->columns(3)->defaultItems(0)
                        ->addActionLabel('Tambah CP')
                        ->schema([
                            TextInput::make('name')->label('Nama')->required(),
                            TextInput::make('purpose')->label('Keperluan')->placeholder('Info volunteer / Kerja sama'),
                            TextInput::make('whatsapp')->label('No. WhatsApp')->tel()->required()->placeholder('0812xxxx'),
                        ]),
                ]),
                Tab::make('T&C Kolaborasi')->icon(Heroicon::OutlinedDocumentText)->schema([
                    Textarea::make('collab_intro')->label('Pengantar')->rows(3),
                    Repeater::make('collab_terms')->label('Poin-poin Terms & Conditions')
                        ->helperText('Urutan di sini = nomor poin di website. Seret untuk mengubah urutan.')
                        ->reorderable()->collapsible()->cloneable()->defaultItems(0)
                        ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                        ->addActionLabel('Tambah poin')
                        ->schema([
                            TextInput::make('title')->label('Judul poin')->required(),
                            Toggle::make('optional')->label('Opsional')->inline(false),
                            RichEditor::make('content')->label('Isi')->required()
                                ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList', 'link'])
                                ->columnSpanFull(),
                            TextInput::make('note')->label('Catatan kecil (opsional)')
                                ->placeholder('Butuh logo? Tinggal reach out ke tim kita ya')->columnSpanFull(),
                        ])->columns(2),
                    Section::make('Contact Person kerja sama')->columns(2)->schema([
                        TextInput::make('collab_contact.name')->label('Nama'),
                        TextInput::make('collab_contact.whatsapp')->label('No. WhatsApp')->tel(),
                    ]),
                    TagsInput::make('collab_partners')->label('Pernah berkolaborasi dengan')
                        ->placeholder('Ketik nama mitra lalu Enter'),
                ]),
            ]),
        ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')->label('Simpan')->submit('save')->keyBindings(['mod+s']),
                    ]),
                ]),
        ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        SiteSetting::put(collect(self::KEYS)->mapWithKeys(fn ($k) => [$k => $data[$k] ?? null])->all());

        Notification::make()->success()->title('Pengaturan disimpan')->send();
    }
}
