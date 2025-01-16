<?php

use App\Jobs\CollectSitesFromNDWJob;
use App\Jobs\CollectSpeedsFromNDWJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Schedule::command('horizon:snapshot')->everyFiveMinutes();
Schedule::job(new CollectSpeedsFromNDWJob())->everyFiveMinutes()->sentryMonitor('CollectSpeedsFromNDWJob');
Schedule::job(new CollectSitesFromNDWJob())->hourly()->sentryMonitor('CollectSitesFromNDWJob');
