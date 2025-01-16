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

class CollectSitesFromNDWJob implements ShouldQueue
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
        $data = $this->requestXML('https://opendata.ndw.nu/measurement.xml.gz');
        $sites = $data['SOAP:Envelope']['SOAP:Body']['d2LogicalModel']['payloadPublication']['measurementSiteTable']['measurementSiteRecord'];

        collect($sites)->chunk(250)->each(function ($sites) {
            SaveSitesToDatabaseJob::dispatch($sites->toArray());
        });
    }
}
