<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    /**
     * Test endpoint healthcheck publik mengembalikan status 200 saat sehat.
     */
    public function test_health_check_returns_healthy_status(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'healthy',
                'checks' => [
                    'database' => 'ok',
                    'cache' => 'ok',
                ],
            ])
            ->assertJsonStructure([
                'status',
                'checks' => [
                    'database',
                    'cache',
                ],
                'timestamp',
            ]);
    }
}

