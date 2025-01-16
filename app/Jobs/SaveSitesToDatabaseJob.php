<?php

namespace App\Jobs;

use App\Models\Characteristic;
use App\Models\Condition;
use App\Models\Site;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SaveSitesToDatabaseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public array $sites)
    {
    }

    public function handle(): void
    {
        $sitesToUpsert = [];
        $characteristicsToInsert = [];
        $conditionsToInsert = [];

        foreach ($this->sites as $site) {
            $sitesToUpsert[] = [
                'ndw_id' => $site['_attributes']['id'],
                'label' => array_key_exists('_value',
                    $site['measurementSiteName']['values']['value']) ? $site['measurementSiteName']['values']['value']['_value'] : null,
                'computation_method' => $site['computationMethod'],
                'type' => $site['measurementEquipmentReference'] ?? null,
                'version_time' => Carbon::parse($site['measurementSiteRecordVersionTime']),
                'side' => $site['measurementSide'] ?? null,
                'lanes' => intval($site['measurementSiteNumberOfLanes']),
                'version' => intval($site['_attributes']['version']),
                'lat' => array_key_exists('locationForDisplay',
                    $site['measurementSiteLocation']) ? floatval($site['measurementSiteLocation']['locationForDisplay']['latitude']) : null,
                'long' => array_key_exists('locationForDisplay',
                    $site['measurementSiteLocation']) ? floatval($site['measurementSiteLocation']['locationForDisplay']['longitude']) : null
            ];


            $characteristics = array_key_exists('_attributes',
                $site['measurementSpecificCharacteristics']) ? [$site['measurementSpecificCharacteristics']] : $site['measurementSpecificCharacteristics'];
            foreach ($characteristics as $characteristic) {
                $info = $characteristic['measurementSpecificCharacteristics'];


                if (array_key_exists('lengthCharacteristic', $info['specificVehicleCharacteristics'])) {


                    $conditions = array_key_exists('vehicleLength',
                        $info['specificVehicleCharacteristics']['lengthCharacteristic']) ? [$info['specificVehicleCharacteristics']['lengthCharacteristic']] : $info['specificVehicleCharacteristics']['lengthCharacteristic'];
                    foreach ($conditions as $condition) {
                        $conditionsToInsert[] = [
                            'characteristic_key' => $characteristic['_attributes']['index'].'_'.$site['_attributes']['id'],
                            'type' => 'length',
                            'value' => $condition['vehicleLength'],
                            'operator' => $condition['comparisonOperator']
                        ];
                    }
                }

                $characteristicsToInsert[] = [
                    'ndw_id' => $site['_attributes']['id'],
                    'index' => intval($characteristic['_attributes']['index']),
                    'key' => $characteristic['_attributes']['index'].'_'.$site['_attributes']['id'],
                    'accuracy' => floatval($info['accuracy']),
                    'period' => intval($info['period']),
                    'lane' => $info['specificLane'] ?? null,
                    'type' => $info['specificMeasurementValueType'],
                    'conditions' => json_encode($info['specificVehicleCharacteristics']) ?? null
                ];

            }
        }


        //Save sites to database
        Site::upsert($sitesToUpsert, ['ndw_id'],
            ['label', 'computation_method', 'type', 'version_time', 'side', 'lanes', 'version', 'lat', 'long']);


        //Save characteristics to database
        $mapping = Site::pluck('id', 'ndw_id');
        $characteristicsToInsert = collect($characteristicsToInsert)->map(function ($characteristic) use ($mapping) {
            $characteristic['site_id'] = $mapping[$characteristic['ndw_id']];
            unset($characteristic['ndw_id']);
            return $characteristic;
        })->toArray();

        collect($characteristicsToInsert)->chunk(500)->each(function ($characteristics) {
            Characteristic::upsert($characteristics->toArray(), ['key'],
                ['index', 'accuracy', 'period', 'lane', 'type', 'conditions']);
        });


//        Save conditions to database
        $conditionMapping = Characteristic::pluck('id', 'key');

        $conditionsToInsert = collect($conditionsToInsert)->map(function ($condition) use ($conditionMapping) {
            $condition['characteristic_id'] = $conditionMapping[$condition['characteristic_key']];
            unset($condition['characteristic_key']);
            return $condition;
        })->toArray();

        collect($conditionsToInsert)->chunk(250)->each(function ($conditions) {
            Condition::upsert($conditions->toArray(), ['characteristic_id', 'type', 'value', 'operator'],
                ['characteristic_id', 'type', 'value', 'operator']);
        });
    }
}
