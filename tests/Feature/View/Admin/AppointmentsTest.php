<?php

namespace Tests\Feature\View\Admin;

use Tests\TestCase;

class AppointmentsTest extends TestCase
{
    /**
     * A basic view test example.
     */
    public function test_it_can_render(): void
    {
        $contents = $this->view('admin.appointments', [
            //
        ]);

        $contents->assertSee('');
    }
}
