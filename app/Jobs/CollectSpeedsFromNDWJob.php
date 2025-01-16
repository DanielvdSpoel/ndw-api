<?php

namespace App\Jobs;

use App\Models\Characteristic;
use App\Models\Site;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Support\CollectsXML;

class CollectSpeedsFromNDWJob implements ShouldQueue
{
    use Queueable, CollectsXML;

    public $timeout = 240;


    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $data = $this->requestXML('https://opendata.ndw.nu/trafficspeed.xml.gz');
        $records = $data['SOAP:Envelope']['SOAP:Body']['d2LogicalModel']['payloadPublication']['siteMeasurements'];

        collect($records)->chunk(500)->each(function ($measurements) {
            SaveSpeedsToDatabaseJob::dispatch($measurements->toArray());
        });
    }
}
