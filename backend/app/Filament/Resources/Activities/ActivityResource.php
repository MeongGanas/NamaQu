<?php

namespace App\Filament\Resources\Activities;

use App\Filament\Resources\Activities\Pages\CreateActivity;
use App\Filament\Resources\Activities\Pages\EditActivity;
use App\Filament\Resources\Activities\Pages\ListActivities;
use App\Filament\Support\Uploads;
use App\Models\Activity;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static string|UnitEnum|null $navigationGroup = 'Konten';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'dokumentasi';

    protected static ?string $pluralModelLabel = 'Dokumentasi Kegiatan';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(3)->components([
            Section::make('Kegiatan')->columnSpan(2)->columns(2)->schema([
                TextInput::make('title')->label('Nama kegiatan')->required()->maxLength(255),
                TextInput::make('slug')->helperText('Kosongkan untuk dibuat otomatis.')->unique(ignoreRecord: true),
                Textarea::make('summary')->label('Deskripsi singkat')->rows(2)->columnSpanFull(),
                RichEditor::make('description')->label('Cerita kegiatan & aktivitas pembelajaran')->columnSpanFull(),
                Uploads::gallery('photos', 'activities/photos', 40)->label('Foto kegiatan')->columnSpanFull()
                    ->helperText('Pilih 10–20 foto terbaik (bisa sekaligus banyak). Foto otomatis dikecilkan sebelum di-upload.'),
                TextInput::make('album_url')->label('Link album lengkap (opsional)')->url()->columnSpanFull()
                    ->placeholder('https://drive.google.com/drive/folders/...')
                    ->helperText('Semua foto lainnya cukup disimpan di Google Drive / Google Photos, lalu tempel link-nya di sini.'),
                Repeater::make('videos')->label('Video (YouTube / Instagram)')->columnSpanFull()
                    ->helperText('Upload video ke YouTube/Instagram dulu, lalu tempel link-nya di sini — supaya tidak membebani server.')
                    ->schema([
                        TextInput::make('title')->label('Judul'),
                        TextInput::make('url')->label('Link')->url()->required(),
                    ])->columns(2)->defaultItems(0)->addActionLabel('Tambah video'),
            ]),
            Section::make('Detail')->schema([
                Select::make('type')->label('Jenis')->options(Activity::TYPES)->default('weekly')->required(),
                DatePicker::make('held_on')->label('Tanggal')->native(false)->required()->default(now()),
                Uploads::image('cover', 'activities', 1600)->label('Foto sampul')
                    ->helperText('Kosongkan untuk memakai foto kegiatan pertama.'),
                Toggle::make('is_published')->label('Tampilkan di website')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('held_on', 'desc')
            ->columns([
                ImageColumn::make('cover')->label('')->disk(config('filesystems.media'))->square(),
                TextColumn::make('title')->label('Kegiatan')->searchable()->wrap(),
                TextColumn::make('type')->label('Jenis')->badge()
                    ->formatStateUsing(fn ($state) => Activity::TYPES[$state] ?? $state)
                    ->color(fn ($state) => $state === 'event' ? 'danger' : 'info'),
                TextColumn::make('held_on')->label('Tanggal')->date('d M Y')->sortable(),
                IconColumn::make('is_published')->label('Tayang')->boolean(),
            ])
            ->filters([
                SelectFilter::make('type')->label('Jenis')->options(Activity::TYPES),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActivities::route('/'),
            'create' => CreateActivity::route('/create'),
            'edit' => EditActivity::route('/{record}/edit'),
        ];
    }
}
