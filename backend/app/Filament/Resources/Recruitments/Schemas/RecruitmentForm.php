<?php

namespace App\Filament\Resources\Recruitments\Schemas;

use App\Filament\Support\Uploads;
use App\Models\Recruitment;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class RecruitmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(3)->components([
            Section::make('Informasi')->columnSpan(2)->columns(2)->schema([
                Select::make('type')->label('Jenis')->options(Recruitment::TYPES)->required()->default('weekly')->live(),
                TextInput::make('title')->label('Judul')->required()->maxLength(255)
                    ->placeholder('Volunteer Minggu, 30 November'),
                TextInput::make('slug')->helperText('Kosongkan untuk dibuat otomatis.')->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('location')->label('Lokasi')->default('Basecamp NamaQu, Keputih, Surabaya'),
                Textarea::make('summary')->label('Ringkasan singkat')->rows(2)->columnSpanFull(),
                RichEditor::make('description')->label('Deskripsi & ketentuan')->columnSpanFull(),
                TagsInput::make('requirements')->label('Persyaratan')->columnSpanFull()
                    ->placeholder('Ketik lalu Enter')
                    ->helperText('Contoh: Follow & like postingan Instagram @namaqu'),
            ]),
            Section::make('Jadwal & pendaftaran')->columnSpan(1)->schema([
                DatePicker::make('activity_date')->label('Tanggal kegiatan')->native(false),
                TextInput::make('activity_time')->label('Jam kegiatan')->placeholder('08.00 - 11.00 WIB'),
                DatePicker::make('registration_opens_at')->label('Pendaftaran dibuka')->native(false),
                DatePicker::make('registration_closes_at')->label('Batas pendaftaran')->native(false)
                    ->afterOrEqual('registration_opens_at'),
                TextInput::make('quota')->label('Kuota volunteer')->numeric()->minValue(1),
                Select::make('registration_method')->label('Cara daftar')->options(Recruitment::METHODS)
                    ->default('form')->required()->live()
                    ->helperText('Mingguan biasanya lewat balas Instagram Story; kegiatan besar lewat formulir.'),
                TextInput::make('external_url')->label('Link pendaftaran / Story')->url()
                    ->visible(fn (Get $get) => in_array($get('registration_method'), ['instagram', 'external']))
                    ->required(fn (Get $get) => $get('registration_method') === 'external'),
                Uploads::image('poster', 'recruitments', 1350)->label('Poster'),
                Toggle::make('is_published')->label('Tampilkan di website')->default(true),
            ]),
        ]);
    }
}
