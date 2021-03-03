<?php

namespace Tests\Feature;

use Schema;
use Tests\TestCase;

class TestCommandTest extends TestCase
{
    public function testTestWithoutParamsCommand()
    {
        Schema::dropIfExists('trades');

        $this->artisan('import --fromCache')
            ->assertExitCode(0);

        $this->artisan('test')
            ->expectsTable(
                ['ID', 'Vehicle ID', 'Seller ID', 'Buyer ID', 'Model ID', 'Sale Date', 'Buy Date'],
                [['1','306687', '82', '245', '84', '2014-01-24 00:00:00', '2013-05-16 00:00:00']],
                'box'
            );
    }

    public function testTestWithExistedID()
    {
        Schema::dropIfExists('trades');

        $this->artisan('import --fromCache')
            ->assertExitCode(0);

        $this->artisan('test --id=1')
            ->expectsTable(
                ['ID', 'Vehicle ID', 'Seller ID', 'Buyer ID', 'Model ID', 'Sale Date', 'Buy Date'],
                [['1','306687', '82', '245', '84', '2014-01-24 00:00:00', '2013-05-16 00:00:00']],
                'box'
            );
    }

    public function testTestWithEmptyTable()
    {
        Schema::dropIfExists('trades');

        $this->artisan('test')
            ->assertExitCode(1);
    }

    public function testTestWithBadIdParam()
    {
        Schema::dropIfExists('trades');

        $this->artisan('import --fromCache')
            ->assertExitCode(0);

        $this->artisan('test --id=foo')
            ->expectsOutput('Wrong parameter id = foo');
    }

    public function testTestWithBadVehicleParam()
    {
        Schema::dropIfExists('trades');

        $this->artisan('import --fromCache')
            ->assertExitCode(0);

        $this->artisan('test --vehicle=111111')
            ->expectsOutput('There are no records with these parameters.');
    }

    public function testTestWithBadBuyerParam()
    {
        Schema::dropIfExists('trades');

        $this->artisan('import --fromCache')
            ->assertExitCode(0);

        $this->artisan('test --buyer=111111')
            ->expectsOutput('There are no records with these parameters.');
    }

    public function testTestWithBadModelParam()
    {
        Schema::dropIfExists('trades');

        $this->artisan('import --fromCache')
            ->assertExitCode(0);

        $this->artisan('test --model=111111')
            ->expectsOutput('There are no records with these parameters.');
    }

    public function testTestWithBadSellerParam()
    {
        Schema::dropIfExists('trades');

        $this->artisan('import --fromCache')
            ->assertExitCode(0);

        $this->artisan('test --seller=111111')
            ->expectsOutput('There are no records with these parameters.');
    }


    public function testTestWithBadNumberParam()
    {
        Schema::dropIfExists('trades');

        $this->artisan('import --fromCache')
            ->assertExitCode(0);

        $this->artisan('test --id=111111')
            ->expectsOutput('There are no records with these parameters.');
    }

}
