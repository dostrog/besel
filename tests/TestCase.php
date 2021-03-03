<?php

namespace Tests;
//namespace Dostrog\Larate\Tests;

use LaravelZero\Framework\Testing\TestCase as BaseTestCase;
use Dostrog\Larate\Facades\LarateFacade;
use Dostrog\Larate\Providers\LarateServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

//abstract class TestCase extends BaseTestCase
//{
//    use CreatesApplication;
//}

class TestCase extends Orchestra
{
    use CreatesApplication;

    //    protected function getPackageProviders($app): array
//    {
//        return [
//            LarateServiceProvider::class,
//        ];
//    }

//    protected function getPackageAliases($app): array
//    {
//        return [
//            'Larate' => LarateFacade::class,
//        ];
//    }

    public function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
