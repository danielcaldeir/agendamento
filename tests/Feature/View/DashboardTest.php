<?php

namespace Tests\Feature\View;

use Tests\TestCase;

class DashboardTest extends TestCase
{
    /**
     * A basic view test example.
     */
    public function test_it_can_render(): void
    {
        $contents = $this->view('dashboard', [
            //
        ]);

        $contents->assertSee('');
    }
}
