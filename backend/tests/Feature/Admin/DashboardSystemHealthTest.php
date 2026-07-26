<?php

namespace Tests\Feature\Admin;

use App\Http\Controllers\Admin\DashboardController;
use ReflectionMethod;
use Tests\TestCase;

class DashboardSystemHealthTest extends TestCase
{
    public function test_database_health_check_identifies_mysql(): void
    {
        $method = new ReflectionMethod(DashboardController::class, 'systemHealth');
        $health = $method->invoke(new DashboardController());

        $this->assertTrue($health['database']['ok']);
        $this->assertSame('MySQL', $health['database']['label']);
    }
}
