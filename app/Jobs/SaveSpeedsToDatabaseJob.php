<?php

namespace App\Jobs;

use App\Models\Characteristic;
use App\Models\Measurement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class SaveSpeedsToDatabaseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public array $measurements)
    {
    }

    public function handle(): void
    {
        // Go through measurements and put them in a format we can semi-easily insert into the database
        $measurementsToInsert = [];
        foreach ($this->measurements as $measurement) {
            $timestamp = Carbon::parse($measurement['measurementTimeDefault'])->toDateTimeString();

            foreach ($measurement['measuredValue'] as $value) {
                $type = $value['measuredValue']['basicData']['_attributes']['type'];
                $actual_value = null;

                if ($type === 'TrafficSpeed') {
                    $actual_value = $value['measuredValue']['basicData']['averageVehicleSpeed']['speed'];
                } else {
                    if ($type === 'TrafficFlow') {
                        $actual_value = $value['measuredValue']['basicData']['vehicleFlow']['vehicleFlowRate'];
                    }
                }

                $measurementsToInsert[] = [
                    'key' => $value['_attributes']['index'].'_'.$measurement['measurementSiteReference']['_attributes']['id'],
                    'type' => $type,
                    'value' => $actual_value,
                    'timestamp' => $timestamp,
                ];

            }
        }


        // We need to find the right characteristic id for each measurement in bulk
        $measurements = collect($measurementsToInsert);
        $mapping = Characteristic::whereIn('key', $measurements->pluck('key'))
            ->get()
            ->mapWithKeys(function (Characteristic $characteristic) {
                return [$characteristic->key => $characteristic->id];
            });


        $cleaned_measurements = collect([]);

        // Drop the measurement key, and add the characteristic_id
        $measurements->each(function ($measurement) use ($mapping, $cleaned_measurements) {
            $key = $measurement['key'];

            if ($mapping->keys()->contains($key)) {
                $measurement['characteristic_id'] = $mapping[$key];
                unset($measurement['key']);

                $cleaned_measurements->push($measurement);
            } else {
                ray($key);
            }
        });

        // Insert the measurements in chunks of 500
        $cleaned_measurements->chunk(500)->each(function ($measurements) {
            Measurement::upsert($measurements->toArray(), ['characteristic_id', 'type', 'timestamp'], ['value']);
        });
    }
}
