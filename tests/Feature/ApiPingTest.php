<?php

it('returns json from GET /api/v1/ping', function () {
    $response = $this->getJson('/api/v1/ping');

    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/json');
    $response->assertJsonStructure([
        'ok',
        'service',
        'timestamp',
        'version',
    ]);
    $response->assertJson([
        'ok' => true,
        'service' => 'tricykab',
    ]);
});
