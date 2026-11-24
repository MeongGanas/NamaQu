<?php

namespace App\Filament\Resources\VolunteerRegistrations\Tables;

use App\Models\VolunteerRegistration;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class VolunteerRegistrationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')->label('Nama')->searchable(),
                TextColumn::make('recruitment.title')->label('Kegiatan')->wrap()->searchable(),
                TextColumn::make('phone')->label('WhatsApp')
                    ->url(fn ($record) => 'https://wa.me/'.preg_replace('/^0/', '62', preg_replace('/\D/', '', $record->phone)), true),
                TextColumn::make('institution')->label('Instansi')->toggleable(),
                TextColumn::make('status')->badge()
                    ->formatStateUsing(fn ($state) => VolunteerRegistration::STATUSES[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        'accepted' => 'success',
                        'rejected' => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('created_at')->label('Mendaftar')->since()->sortable(),
            ])
            ->filters([
                SelectFilter::make('recruitment')->label('Kegiatan')->relationship('recruitment', 'title'),
                SelectFilter::make('status')->options(VolunteerRegistration::STATUSES),
            ])
            ->recordActions([EditAction::make()->label('Tinjau')])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('accept')->label('Tandai diterima')->icon('heroicon-o-check')
                        ->action(fn (Collection $records) => $records->each->update(['status' => 'accepted']))
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('reject')->label('Tandai ditolak')->icon('heroicon-o-x-mark')->color('danger')
                        ->action(fn (Collection $records) => $records->each->update(['status' => 'rejected']))
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
