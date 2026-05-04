<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Driver search radius (meters)
    |--------------------------------------------------------------------------
    |
    | Used in POST /bookings JSON until dispatch assigns a driver.
    |
    */

    'search_radius_meters' => (int) env('BOOKING_SEARCH_RADIUS_METERS', 1000),

    /*
    |--------------------------------------------------------------------------
    | Average speed for ETA (km/h)
    |--------------------------------------------------------------------------
    |
    | Rough duration estimate from straight-line distance (PRD allows heuristic MVP).
    |
    */

    'average_speed_kmh' => (float) env('BOOKING_AVERAGE_SPEED_KMH', 25),

];
