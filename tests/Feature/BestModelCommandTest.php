<?php

namespace Tests\Feature;

use Schema;
use Tests\TestCase;

class BestModelCommandTest extends TestCase
{
    public function testTestWithExistedID()
    {
        Schema::dropIfExists('trades');

        $this->artisan('import --fromCache')
            ->assertExitCode(0);

        $this->artisan('best:model 111111')
            ->expectsOutput('No suitable records found.');
    }

    public function testTestWithBadIDinArgs()
    {
        Schema::dropIfExists('trades');

        $this->artisan('import --fromCache')
            ->assertExitCode(0);

        $this->expectException('InvalidArgumentException');

        $this->artisan('best:model foo');
    }
}
