<?php
declare(strict_types=1);

namespace App\Commands;

use App\Services\Tester;
use Illuminate\Support\Arr;
use LaravelZero\Framework\Commands\Command;
use Throwable;

class TestCommand extends Command
{
    use EnsureEnvironment;

    /**
     * @inheritdoc
     */
    protected $signature = 'test
    {--id= : Test by primary ID (integer)}
    {--vehicle= : Test by Vehicle Id (integer)}
    {--seller= : Test by Seller Id (integer)}
    {--buyer= : Test by Buyer Id (integer)}
    {--model= : Test by Model Id (integer)}';

    /**
     * @inheritdoc
     */
    protected $description = 'Check for record in Trades table';

    public function handle()
    {
        $this->title($this->description);

        $this->ensureEnvironment();

        try {
            $tester = new Tester($this->output);
            $tester->check(Arr::only($this->options(), [
                'id', 'vehicle', 'seller', 'buyer', 'model'
            ]));
        } catch (Throwable $throwable) {
            $this-> error($throwable->getMessage());

            return 1;
        }

        return 0;
    }
}
