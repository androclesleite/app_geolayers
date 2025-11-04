<?php

namespace App\Filament\Resources\Layers\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LayerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(100)
                    ->label('Nome da Camada'),

                FileUpload::make('geojson_file')
                    ->label('Arquivo GeoJSON')
                    ->acceptedFileTypes(['application/json', 'application/geo+json', 'text/plain', '.json', '.geojson'])
                    ->maxSize(10240)
                    ->disk(null)
                    ->directory(null)
                    ->storeFiles(false)
                    ->required(fn ($context) => $context === 'create')
                    ->helperText('Upload de arquivo GeoJSON (máx. 10MB). Obrigatório ao criar, opcional ao editar.'),
            ]);
    }
}
