<?php
declare(strict_types=1);

namespace App\Commands;

use App\Services\BestMonths;
use LaravelZero\Framework\Commands\Command;

class BestMonthsCommand extends Command
{
    use EnsureEnvironment;

    public const HEADER = ['Model', 'Quantity', 'Month', 'Year'];

    /**
     * @inheritdoc
     */
    protected $signature = 'best:months
    {model_id : Model Id for which the best three months are requested}
    {months=3 : Period in month for search}';

    /**
     * @inheritdoc
     */
    protected $description = 'Best months for specified model';

    public function handle(): int
    {
        $this->ensureEnvironment();

        $modelId = $this->argument('model_id');
        $months = $this->argument('months');

        $this->title("Best {$months} months for model {$modelId}");

        $months = (new BestMonths())->execute( $modelId, $months );

        if (count($months) === 0){
            $this->comment("No suitable records found.");

            return 1;
        }

        $this->table(self::HEADER, $months, 'box');

        return 0;
    }
}
