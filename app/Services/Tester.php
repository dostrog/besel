<?php
declare(strict_types=1);

namespace App\Services;

use Illuminate\Console\OutputStyle;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class Tester
{
    public const HEADER = ['ID', 'Vehicle ID', 'Seller ID', 'Buyer ID', 'Model ID', 'Sale Date', 'Buy Date'];
    private OutputStyle $output;

    public function __construct(OutputStyle $output)
    {
        $this->output = $output;
    }

    public function check(array $arguments): void
    {
        if (collect($arguments)->every(fn($value) => $value === null)) {
            $this->returnLastRecord();
            return;
        }

        $wherePart = $this->getWheres($arguments);

        if ($wherePart === '1') {
            $this->output->writeln("There are no records with these parameters.");
            $this->output->newLine();

            return;
        }

        $this->returnWithWhere($wherePart);
    }

    private function returnLastRecord(): void
    {
        $this->output->writeln('Last inserted record:');
        $this->output->newLine();

        $record = array_values((array)DB::table('trades')->latest()->limit(1)->get([
            'id', 'vehicle_id', 'inhouse_seller_id', 'buyer_id', 'model_id', 'sale_date', 'buy_date'
        ])->toArray()[0]);

        $this->output->table(self::HEADER, [$record]);
    }

    private function getWheres(array $arguments): string
    {
        collect($arguments)->each(function ($value, $key) use (&$result) {
            if ($value === null) {
                return;
            }
            if (is_numeric($value) === false) {
                throw new InvalidArgumentException("Wrong parameter {$key} = {$value}");
            }

            switch ($key) {
                case 'id':
                    $result .= sprintf("id = %s and ", (int) $value);
                    break;
                case 'vehicle':
                    $result .= sprintf("vehicle_id = %s and ", (int) $value);
                    break;
                case 'seller':
                    $result .= sprintf("inhouse_seller_id = %s and ", (int) $value);
                    break;
                case 'buyer':
                    $result .= sprintf("buyer_id = %s and ", (int) $value);
                    break;
                case 'model':
                    $result .= sprintf("model_id = %s and ", (int) $value);
                    break;
            }
        });

        return $result . "1";
    }

    private function returnWithWhere(string $wherePart, int $limit = 5): void
    {
        $record = DB::table('trades')
            ->whereRaw($wherePart)
            ->latest()->limit($limit)->get([
            'id', 'vehicle_id', 'inhouse_seller_id', 'buyer_id', 'model_id', 'sale_date', 'buy_date'
        ]);

        if ($record->isEmpty()) {
            $this->output->writeln("There are no records with these parameters.");
            $this->output->newLine();

            return;
        }

        $this->output->writeln('There is record(s) with parameters. '. $record->count() . " latest :");
        $this->output->newLine();

        $toRows = [];

        $record->each(function($value) use (&$toRows){
            $toRows[] = array_values((array)$value);
        });

        $this->output->table(self::HEADER, $toRows);
    }
}
