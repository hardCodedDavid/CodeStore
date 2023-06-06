<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    use UUID;
    use MassPrunable;

    /**
     * Get the prunable model query.
     *
     * @return Builder
     */
    public function prunable(): Builder
    {
        $period = now()->subMonths(3);

        return static::query()->where('created_at', '<=', $period)->where(
            fn ($query) => $query->whereNull('last_used_at')->orWhere('last_used_at', '<=', $period)
        );
    }
}
