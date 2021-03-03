<?php

namespace Tests\Feature;

use Schema;
use Tests\TestCase;

class BestMonthsCommandTest extends TestCase
{
    public function testBestMonthsWithExistedID()
    {
        Schema::dropIfExists('trades');

        $this->artisan('import --fromCache')
            ->assertExitCode(0);

        $this->artisan('best:months 84 3')
            ->expectsTable(
                ['Model', 'Quantity', 'Month', 'Year'],
                [
                    ['84','237', '4', '2014'],
                    ['84','403', '7', '2014'],
                    ['84','282', '12', '2014'],
                ],
                'box'
            );
    }

    public function testBestMonthsWithBadIDinArgs()
    {
        Schema::dropIfExists('trades');

        $this->artisan('import --fromCache')
            ->assertExitCode(0);

        $this->expectException('InvalidArgumentException');

        $this->artisan('best:model foo');
    }

    public function testBestMonthsWithWrongIDinArgs()
    {
        Schema::dropIfExists('trades');

        $this->artisan('import --fromCache')
            ->assertExitCode(0);

        $this->artisan('best:months 844 3')
            ->expectsOutput('No suitable records found.');
    }
}
