<?php

namespace App\Filament\Resources\VolunteerRegistrations;

use App\Filament\Resources\VolunteerRegistrations\Pages\EditVolunteerRegistration;
use App\Filament\Resources\VolunteerRegistrations\Pages\ListVolunteerRegistrations;
use App\Filament\Resources\VolunteerRegistrations\Schemas\VolunteerRegistrationForm;
use App\Filament\Resources\VolunteerRegistrations\Tables\VolunteerRegistrationsTable;
use App\Models\VolunteerRegistration;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class VolunteerRegistrationResource extends Resource
{
    protected static ?string $model = VolunteerRegistration::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxArrowDown;

    protected static string|UnitEnum|null $navigationGroup = 'Volunteer';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'pendaftar';

    protected static ?string $pluralModelLabel = 'Pendaftar Volunteer';

    public static function getNavigationBadge(): ?string
    {
        $count = VolunteerRegistration::where('status', 'pending')->count();

        return $count ? (string) $count : null;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return VolunteerRegistrationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VolunteerRegistrationsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVolunteerRegistrations::route('/'),
            'edit' => EditVolunteerRegistration::route('/{record}/edit'),
        ];
    }
}
