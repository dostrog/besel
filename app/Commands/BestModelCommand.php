<?php
declare(strict_types=1);

namespace App\Commands;

use App\Services\BestModel;
use LaravelZero\Framework\Commands\Command;

class BestModelCommand extends Command
{
    use EnsureEnvironment;

    public const HEADER = ['#', 'Model', 'Quantity', 'Buyer ID', 'Name', 'Surname'];

    /**
     * @inheritdoc
     */
    protected $signature = 'best:model
    {buyer_id? : Buyer Id for which the model are requested}';
    /**
     * @inheritdoc
     */
    protected $description = 'Best selling model per client';

    public function handle(): int
    {
        $this->ensureEnvironment();

        $this->title("Best selling model per customer");

        $models = (new BestModel())->execute($this->argument('buyer_id'));

        if (count($models) === 0){
            $this->comment("No suitable records found.");

            return 1;
        }

        $this->table(self::HEADER, $models, 'box');

        return 0;
    }
}
