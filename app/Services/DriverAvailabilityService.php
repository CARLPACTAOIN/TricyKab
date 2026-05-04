<?php

namespace App\Services;

use App\Models\Driver;

class DriverAvailabilityService
{
    /**
     * @param  array{driver_status: string, latitude?: float|null, longitude?: float|null, accuracy_meters?: float|null}  $data
     */
    public function updateFromRequest(Driver $driver, array $data): Driver
    {
        $online = strtoupper((string) $data['driver_status']) === 'ONLINE';

        $driver->availability_status = $online ? Driver::AVAILABILITY_ONLINE : Driver::AVAILABILITY_OFFLINE;
        $driver->last_availability_at = now();

        if (isset($data['latitude'], $data['longitude'])) {
            $driver->last_latitude = $data['latitude'];
            $driver->last_longitude = $data['longitude'];
        }

        if (array_key_exists('accuracy_meters', $data) && $data['accuracy_meters'] !== null) {
            $driver->last_accuracy_meters = $data['accuracy_meters'];
        }

        $driver->save();

        return $driver->fresh();
    }
}
