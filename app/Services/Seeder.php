<?php
declare(strict_types=1);

namespace App\Services;

use Faker\Factory;
use Illuminate\Console\OutputStyle;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Symfony\Component\Console\Helper\ProgressBar;
use Throwable;

final class Seeder
{
    public const CHUNK_SIZE = 512;
    public const TRADES_TABLE_NAME = 'trades';
    public const BUYERS_TABLE_NAME = 'buyers';

    private ?OutputStyle $output;
    private Importer $importer;

    public function __construct(Importer $importer, OutputStyle $output = null)
    {
        $this->output = $output;
        $this->importer = $importer;
    }

    public function seedDatabase()
    {
        $bar = $this->output->createProgressBar();

        $this->seedTradesTable($bar);
        $this->seedBuyersTable($bar);
    }

    public function prepareDatabase(): self
    {
        try {
            $this->prepareTradesTable();
            $this->prepareBuyersTable();
        } catch (Throwable $throwable) {
            throw new RuntimeException($throwable->getMessage());
        }

        return $this;
    }

    private function seedTradesTable(ProgressBar $bar = null): void
    {
        $trades = $this->importer->getDataCollection()->toArray();

        if (isset($bar)) {
            $bar->setMaxSteps(count($trades));
            $bar->setProgressCharacter('🚙');
        }

        $this->output->writeln("Seeding `" . self::TRADES_TABLE_NAME. "` by <comment>" . count($trades) . "</comment> row(s). Chunked by " . self::CHUNK_SIZE . " bytes.");
        $this->output->newLine();

        $bar->start();

        foreach (array_chunk($trades, self::CHUNK_SIZE) as $chunk) {
            DB::table(self::TRADES_TABLE_NAME)->insert( $chunk );
            $bar->advance(count($chunk));
        }

        $bar->finish();
        $this->output->newLine(2);
    }

    private function seedBuyersTable(ProgressBar $bar = null): void
    {
        $buyers = $this->importer->getDataCollection()->unique('buyer_id');

        if (isset($bar)) {
            $bar->setMaxSteps(count($buyers));
        }

        $faker = Factory::create(config('besel.faker_locale'));

        $this->output->writeln("Seeding `" . self::BUYERS_TABLE_NAME. "` by <comment>" . count($buyers) . "</comment> row(s) (with fake Name, Surname)");
        $this->output->newLine();

        $this->output->newLine();

        $bar->start();

        $buyers->each(function($buyer) use ($faker) {
            $fakeName = explode(' ', $faker->name());

            $record['name'] = $fakeName[count($fakeName)-2];
            $record['surname'] = $fakeName[count($fakeName)-1];
            $record['id'] = $buyer['buyer_id'];

            DB::table(self::BUYERS_TABLE_NAME)->insert( $record );
        });

        $bar->finish();
        $this->output->newLine(2);
    }

    private function prepareTradesTable(): void
    {
        if (isset($this->output)) {
            $this->output->writeln("Create (<comment>with clear</comment>) main table: `" . self::TRADES_TABLE_NAME . "`");
        }

        Schema::dropIfExists(self::TRADES_TABLE_NAME);

        Schema::create(self::TRADES_TABLE_NAME, function (Blueprint $table) {
            $table->id();
            $table->bigInteger('vehicle_id')->unique();
            $table->bigInteger('inhouse_seller_id');
            $table->bigInteger('buyer_id');
            $table->bigInteger('model_id');
            $table->dateTime('sale_date');
            $table->dateTime('buy_date');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    private function prepareBuyersTable(): void
    {
        if (isset($this->output)) {
            $this->output->writeln("Create (<comment>with clear</comment>) table: `" . self::BUYERS_TABLE_NAME . "`");
        }

        Schema::dropIfExists(self::BUYERS_TABLE_NAME);

        Schema::create(self::BUYERS_TABLE_NAME, function (Blueprint $table) {
            $table->bigInteger('id')->unique();
            $table->string('name');
            $table->string('surname');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }
}
