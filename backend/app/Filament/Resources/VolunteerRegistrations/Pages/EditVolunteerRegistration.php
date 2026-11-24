<?php

namespace App\Filament\Resources\VolunteerRegistrations\Pages;

use App\Filament\Resources\VolunteerRegistrations\VolunteerRegistrationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVolunteerRegistration extends EditRecord
{
    protected static string $resource = VolunteerRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
