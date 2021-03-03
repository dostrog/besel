<?php

namespace Tests\Feature;

use Schema;
use Tests\TestCase;

class AddCommandTest extends TestCase
{
    public function testAddFullRecordCommand()
    {
        Schema::dropIfExists('trades');

        $this->artisan('import --fromCache')
            ->assertExitCode(0);

        $this->assertDatabaseMissing('trades', [
            'vehicle_id' => 324,
        ]);

        $this->artisan('add 324 2124 3123 4123 2020-11-11 2020-11-01')
            ->assertExitCode(0);

        $this->assertDatabaseHas('trades', [
            'vehicle_id' => 324,
        ]);
    }

    public function testAddCommanWithFewArgs()
    {
        Schema::dropIfExists('trades');

        $this->artisan('import --fromCache')
            ->assertExitCode(0);

        $this->expectException('RuntimeException');

        $this->artisan('add 324 2124 3123 4123 2020-11-11')
            ->expectsOutput('Not enough arguments (missing: "BuyDate").')
            ->assertExitCode(0);
    }

    public function testAddCommanWithErrorFromService()
    {
        Schema::dropIfExists('trades');

        $this->artisan('add 324 2124 3123 4123 2020-11-11 2020-11-01')
            ->assertExitCode(1);
    }


    public function testAddCommanWithErrorFromServiceBadDate()
    {
        Schema::dropIfExists('trades');

        $this->artisan('add 324 2124 3123 4123 2020-11-11 2020-11-01foo')
            ->assertExitCode(1);
    }

    public function testAddCommanWithErrorFromServiceBuyersInsert()
    {
        Schema::dropIfExists('trades');
        Schema::dropIfExists('buyers');

        $this->artisan('import --fromCache')
            ->assertExitCode(0);

        Schema::dropIfExists('buyers');

        $this->artisan('add 324 2124 3123 4123 2020-11-11 2020-11-01')
            ->assertExitCode(1);
    }
}
