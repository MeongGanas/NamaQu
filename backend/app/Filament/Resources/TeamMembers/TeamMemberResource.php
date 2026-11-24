<?php

namespace App\Filament\Resources\TeamMembers;

use App\Filament\Resources\TeamMembers\Pages\CreateTeamMember;
use App\Filament\Resources\TeamMembers\Pages\EditTeamMember;
use App\Filament\Resources\TeamMembers\Pages\ListTeamMembers;
use App\Filament\Support\Uploads;
use App\Models\TeamMember;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
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
use Filament\Tables\Table;
use UnitEnum;

class TeamMemberResource extends Resource
{
    protected static ?string $model = TeamMember::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static string|UnitEnum|null $navigationGroup = 'Konten';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'pengurus';

    protected static ?string $pluralModelLabel = 'Biodata Pengurus';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(3)->components([
            Section::make('Profil')->columnSpan(2)->columns(2)->schema([
                TextInput::make('name')->label('Nama lengkap')->required(),
                TextInput::make('nickname')->label('Nama panggilan'),
                TextInput::make('role')->label('Jabatan / peran')->required()->placeholder('Founder & Koordinator Pendidikan'),
                Toggle::make('is_founder')->label('Founder')->inline(false),
                Textarea::make('responsibilities')->label('Tanggung jawab di NamaQu')->rows(4)->columnSpanFull(),
                TextInput::make('instagram')->label('Instagram (username)')->prefix('@'),
                TextInput::make('linkedin')->label('LinkedIn (URL)')->url(),
            ]),
            Section::make('Foto & tampilan')->schema([
                Uploads::image('photo', 'team', 800)->label('Foto profil')->avatar()->imageEditor(),
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
                ImageColumn::make('photo')->label('')->disk(config('filesystems.media'))->circular(),
                TextColumn::make('name')->label('Nama')->searchable(),
                TextColumn::make('role')->label('Peran')->searchable(),
                IconColumn::make('is_founder')->label('Founder')->boolean(),
                IconColumn::make('is_published')->label('Tayang')->boolean(),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTeamMembers::route('/'),
            'create' => CreateTeamMember::route('/create'),
            'edit' => EditTeamMember::route('/{record}/edit'),
        ];
    }
}
