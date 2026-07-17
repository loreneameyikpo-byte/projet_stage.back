<?php

namespace Tests\Feature;

use Tests\TestCase;

class SemoaRoutesTest extends TestCase
{
    public function test_semoa_callback_route_is_registered(): void
    {
        $route = $this->app['router']->getRoutes()->getByName('api.semoa.callback');

        $this->assertNotNull($route);
    }

    public function test_semoa_authentication_route_is_public(): void
    {
        $response = $this->postJson('/api/authentification');

        $this->assertNotSame(401, $response->getStatusCode());
    }
}
