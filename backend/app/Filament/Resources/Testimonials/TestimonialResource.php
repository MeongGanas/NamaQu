<?php

namespace App\Filament\Resources\Testimonials;

use App\Filament\Resources\Testimonials\Pages\CreateTestimonial;
use App\Filament\Resources\Testimonials\Pages\EditTestimonial;
use App\Filament\Resources\Testimonials\Pages\ListTestimonials;
use App\Filament\Support\Uploads;
use App\Models\Testimonial;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static string|UnitEnum|null $navigationGroup = 'Volunteer';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'kesan & pesan';

    protected static ?string $pluralModelLabel = 'Kesan & Pesan';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(3)->components([
            Section::make('Testimoni')->columnSpan(2)->columns(2)->schema([
                TextInput::make('name')->label('Nama volunteer')->required(),
                TextInput::make('institution')->label('Kampus / instansi'),
                TextInput::make('activity_name')->label('Kegiatan yang diikuti')->required()
                    ->placeholder('Volunteer Mingguan / Hari Anak 2026'),
                Select::make('activity_id')->label('Tautkan ke dokumentasi (opsional)')
                    ->relationship('activity', 'title')->searchable()->preload(),
                Textarea::make('message')->label('Kesan & pesan')->required()->rows(5)->columnSpanFull(),
            ]),
            Section::make('Persetujuan & tampilan')->schema([
                Toggle::make('show_full_name')->label('Boleh tampilkan nama lengkap')->default(true)
                    ->helperText('Jika mati, nama ditampilkan sebagai "Rina A."'),
                Uploads::image('photo', 'testimonials', 600)->label('Foto (opsional)')->avatar(),
                Toggle::make('is_published')->label('Tampilkan di website')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')->label('Nama')->searchable(),
                TextColumn::make('activity_name')->label('Kegiatan'),
                TextColumn::make('message')->label('Pesan')->limit(60)->wrap(),
                IconColumn::make('show_full_name')->label('Nama lengkap')->boolean(),
                IconColumn::make('is_published')->label('Tayang')->boolean(),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTestimonials::route('/'),
            'create' => CreateTestimonial::route('/create'),
            'edit' => EditTestimonial::route('/{record}/edit'),
        ];
    }
}
