<?php

namespace App\Console\Commands;

use App\Models\Common\Language;
use Illuminate\Console\Command;

class SyncLanguages extends Command
{
    protected $signature = 'languages:sync {codes?* : Language codes to sync (e.g. en no da)}';

    protected $description = 'Ensure language records exist for provided codes';

    public function handle(): void
    {
        $codes = $this->argument('codes') ?: config('app.supported_locales', ['en']);

        foreach ($codes as $code) {
            Language::firstOrCreate(['code' => $code]);
        }

        $this->info('Languages synced: '.implode(', ', $codes));
    }
}
