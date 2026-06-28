<?php

namespace App\Filament\Resources\RaffleConfigurationResource\Pages;

use App\Filament\Resources\RaffleConfigurationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRaffleConfiguration extends EditRecord
{
    protected static string $resource = RaffleConfigurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * ANTES DE CARGAR EL FORMULARIO: Toma el valor de la columna 'value' y lo asigna al campo virtual correspondiente según el tipo
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $type = $data['type'] ?? 'string';
        $rawValues = $data['value'];

        // Inicializamos los campos virtuales vacíos
        $data['value_string'] = null;
        $data['value_integer'] = null;
        $data['value_boolean'] = false;
        $data['value_json'] = [];
        $data['value_array'] = [];

        // Asignamos el valor de la BD al campo virtual que le corresponde
        switch ($type) {
            case 'string':
                $data['value_string'] = (string)$rawValues;
                break;
            case 'integer':
                $data['value_integer'] = (int)$rawValues;
                break;
            case 'boolean':
                $data['value_boolean'] = (bool)$rawValues;
                break;
            case 'json':
                $data['value_json'] = is_array($rawValues) ? $rawValues : (json_decode($rawValues, true) ?: []);
                break;
            case 'array':
                $data['value_array'] = is_array($rawValues) ? $rawValues : (json_decode($rawValues, true) ?: []);
                break;
        }

        return $data;
    }

    /**
     * ANTES DE GUARDAR: Toma el valor del campo virtual activo y lo guarda en la columna 'value'
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Obtenemos el tipo directo del registro real en la BD, ignorando el formulario
        $type = $this->record->type ?? 'string';

        switch ($type) {
            case 'string':
                $data['value'] = $data['value_string'] ?? null;
                break;
            case 'integer':
                $data['value'] = isset($data['value_integer']) ? (int)$data['value_integer'] : null;
                break;
            case 'boolean':
                $data['value'] = isset($data['value_boolean']) ? (bool)$data['value_boolean'] : false;
                break;
            case 'json':
                $data['value'] = $data['value_json'] ?? [];
                break;
            case 'array':
                // Forzamos a guardar como un array plano de valores limpios
                $data['value'] = isset($data['value_array']) ? array_values((array)$data['value_array']) : [];
                break;
        }

        // Eliminamos de forma segura las llaves virtuales que existan
        $virtualFields = ['value_string', 'value_integer', 'value_boolean', 'value_json', 'value_array'];
        foreach ($virtualFields as $field) {
            if (array_key_exists($field, $data)) {
                unset($data[$field]);
            }
        }

        return $data;
    }
}
