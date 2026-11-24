<?php

namespace App\Filament\Resources\VolunteerRegistrations\Schemas;

use App\Models\VolunteerRegistration;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VolunteerRegistrationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(3)->components([
            Section::make('Data pendaftar')->columnSpan(2)->columns(2)->schema([
                Select::make('recruitment_id')->label('Kegiatan')->relationship('recruitment', 'title')->disabled(),
                TextInput::make('name')->label('Nama')->disabled(),
                TextInput::make('email')->disabled(),
                TextInput::make('phone')->label('WhatsApp')->disabled(),
                TextInput::make('institution')->label('Kampus / instansi')->disabled(),
                TextInput::make('instagram')->disabled(),
                Textarea::make('motivation')->label('Motivasi')->disabled()->columnSpanFull(),
            ]),
            Section::make('Tindak lanjut')->schema([
                Select::make('status')->options(VolunteerRegistration::STATUSES)->required(),
                Textarea::make('admin_note')->label('Catatan pengurus')->rows(4),
            ]),
        ]);
    }
}
