<?php

namespace App\Filament\Resources\Recruitments\Tables;

use App\Models\Recruitment;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RecruitmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('activity_date', 'desc')
            ->columns([
                TextColumn::make('title')->label('Judul')->searchable()->wrap(),
                TextColumn::make('type')->label('Jenis')->badge()
                    ->formatStateUsing(fn ($state) => Recruitment::TYPES[$state] ?? $state)
                    ->color(fn ($state) => $state === 'event' ? 'danger' : 'info'),
                TextColumn::make('activity_date')->label('Tanggal')->date('d M Y')->sortable(),
                TextColumn::make('registration_closes_at')->label('Batas daftar')->date('d M Y')->sortable(),
                TextColumn::make('registrations_count')->counts('registrations')->label('Pendaftar')->badge(),
                IconColumn::make('is_published')->label('Tayang')->boolean(),
            ])
            ->filters([
                SelectFilter::make('type')->label('Jenis')->options(Recruitment::TYPES),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
