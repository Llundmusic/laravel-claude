<?php

namespace App\Services;

use App\Models\Common\Language;
use Illuminate\Support\Collection;

class LanguageService
{
    /** @return Collection<Language> */
    public function all(): Collection
    {
        return Language::orderBy('code')->get();
    }

    public function syncFromConfig(): void
    {
        $codes = config('app.supported_locales', ['en']);

        foreach ($codes as $code) {
            Language::firstOrCreate(['code' => $code]);
        }
    }

    /** @return array<string, string> */
    public function forSelect(): array
    {
        return $this->all()->pluck('code', 'code')->toArray();
    }
}
