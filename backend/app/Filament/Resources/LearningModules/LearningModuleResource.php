<?php

namespace App\Filament\Resources\LearningModules;

use App\Filament\Resources\LearningModules\Pages\CreateLearningModule;
use App\Filament\Resources\LearningModules\Pages\EditLearningModule;
use App\Filament\Resources\LearningModules\Pages\ListLearningModules;
use App\Filament\Support\Uploads;
use App\Models\LearningModule;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class LearningModuleResource extends Resource
{
    protected static ?string $model = LearningModule::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static string|UnitEnum|null $navigationGroup = 'Konten';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'modul';

    protected static ?string $pluralModelLabel = 'Modul Pembelajaran';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(3)->components([
            Section::make('Materi')->columnSpan(2)->columns(2)->schema([
                TextInput::make('title')->label('Judul materi')->required()->maxLength(255),
                TextInput::make('slug')->helperText('Kosongkan untuk dibuat otomatis.')->unique(ignoreRecord: true),
                Textarea::make('summary')->label('Deskripsi singkat')->rows(2)->columnSpanFull(),
                TagsInput::make('objectives')->label('Tujuan pembelajaran')->columnSpanFull()
                    ->placeholder('Ketik lalu Enter')
                    ->helperText('Contoh: Anak dapat membaca suku kata berpola KV-KV'),
                RichEditor::make('content')->label('Isi / ringkasan materi')->columnSpanFull(),
                Uploads::gallery('gallery', 'modules/gallery')->label('Dokumentasi pendukung')->columnSpanFull(),
            ]),
            Section::make('Pengelompokan')->schema([
                Select::make('level')->label('Tingkat')->options(LearningModule::LEVELS)->required(),
                Select::make('category')->label('Jenis materi')->options(LearningModule::CATEGORIES)->required()->live(),
                Select::make('difficulty')->label('Tingkat kesulitan')->options(LearningModule::DIFFICULTIES)->default(1)->required(),
                TextInput::make('target')->label('Target usia / kemampuan')->placeholder('Kelas 1-2 SD, belum lancar membaca'),
                TextInput::make('external_url')->label('Link website game')->url()
                    ->visible(fn (Get $get) => $get('category') === 'game_coding'),
                Uploads::image('cover', 'modules', 1200)->label('Gambar sampul'),
                TextInput::make('sort_order')->label('Urutan')->numeric()->default(0),
                Toggle::make('is_published')->label('Tampilkan di website')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                ImageColumn::make('cover')->label('')->disk(config('filesystems.media'))->square(),
                TextColumn::make('title')->label('Judul')->searchable()->wrap(),
                TextColumn::make('level')->label('Tingkat')->badge()
                    ->formatStateUsing(fn ($state) => LearningModule::LEVELS[$state] ?? $state),
                TextColumn::make('category')->label('Jenis')
                    ->formatStateUsing(fn ($state) => LearningModule::CATEGORIES[$state] ?? $state),
                TextColumn::make('difficulty')->label('Kesulitan')
                    ->formatStateUsing(fn ($state) => LearningModule::DIFFICULTIES[$state] ?? $state),
                IconColumn::make('is_published')->label('Tayang')->boolean(),
            ])
            ->filters([
                SelectFilter::make('level')->label('Tingkat')->options(LearningModule::LEVELS),
                SelectFilter::make('category')->label('Jenis')->options(LearningModule::CATEGORIES),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLearningModules::route('/'),
            'create' => CreateLearningModule::route('/create'),
            'edit' => EditLearningModule::route('/{record}/edit'),
        ];
    }
}
