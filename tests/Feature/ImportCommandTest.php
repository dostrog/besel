<?php

namespace Tests\Feature;

use Exception;
use Illuminate\Http\Client\Factory;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Schema;
use Tests\TestCase;

class ImportCommandTest extends TestCase
{
    public function testImportFromCacheCommand()
    {
        Schema::dropIfExists('trades');

        self::assertFalse(Schema::hasTable('trades'));

        $this->artisan('import --fromCache')
            ->assertExitCode(0);

        self::assertTrue(Schema::hasTable('trades'));

        $this->assertDatabaseHas('trades', [
            'id' => 1,
        ]);
    }

    public function testImportWithBadConfig()
    {
        Schema::dropIfExists('trades');

        self::assertFalse(Schema::hasTable('trades'));

        Config::set('besel.data_cache_filename', 'foo');

        $this->artisan('import --fromCache')
            ->assertExitCode(1);

        self::assertFalse(Schema::hasTable('trades'));
    }

    public function testImportFromSiteError()
    {
        Schema::dropIfExists('trades');

        $httpClient = $this->mock(Factory::class);
        $httpClient->shouldReceive('get')
            ->andThrow(Exception::class);

        $this->artisan('import')
            ->assertExitCode(1);
    }

    public function testImportFromSiteBadData()
    {
        $data = <<<'DATA'
        VehicleID,InhouseSellerID,BuyerID,ModelID,SaleDate,BuyDate
        <br>306687,82,245,84,2014-01-24,2013-05-16foo
        <br>306689,82,245,84,2014-01-24,2013-05-16
        DATA;

        Schema::dropIfExists('trades');

        Http::fake([
            'https://admin.b2b-carmarket.com/*' => Http::response($data, 200),
        ]);

        $this->artisan('import')
            ->assertExitCode(1);
    }

    public function testImportFromSiteNoData()
    {
        Schema::dropIfExists('trades');

        Http::fake([
            'https://admin.b2b-carmarket.com/*' => Http::response('', 200),
        ]);

        $this->artisan('import')
            ->assertExitCode(1);
    }

    public function testImportFromSiteServerError()
    {
        Schema::dropIfExists('trades');

        Http::fake([
            'https://admin.b2b-carmarket.com/*' => Http::response('1', 503),
        ]);

        $this->artisan('import')
            ->assertExitCode(1);
    }

    public function testImportFromSite()
    {
        Schema::dropIfExists('trades');

        $this->artisan('import')
            ->assertExitCode(0);
    }

}
