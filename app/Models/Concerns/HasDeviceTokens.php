<?php

namespace App\Models\Concerns;

use App\Models\DeviceToken;

trait HasDeviceTokens
{
    public function deviceTokens()
    {
        return $this->morphMany(DeviceToken::class, 'tokenable');
    }

    public function routeNotificationForFcm(): array
    {
        return $this->deviceTokens()->pluck('token')->all();
    }

    public function routeNotificationForWhatsApp(): ?string
    {
        return $this->phone ?? null;
    }
}
