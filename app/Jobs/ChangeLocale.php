<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;

class ChangeLocale extends Job implements SelfHandling
{
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
    	// Change language in front-end
        session()->set('locale', session('locale') == 'fr' ? 'en' : 'fr');
    }
}
