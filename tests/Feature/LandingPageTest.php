<?php

namespace Tests\Feature;

use Tests\TestCase;

class LandingPageTest extends TestCase
{
    public function test_landing_page_renders_react_mount_point(): void
    {
        $this->withoutVite();

        $response = $this->get(route('landing'));

        $response->assertOk();
        $response->assertSee('Tumbuh UMKM', false);
        $response->assertSee('<div id="app" data-page="landing"></div>', false);
    }
}
