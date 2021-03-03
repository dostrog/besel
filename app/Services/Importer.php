<?php
declare(strict_types=1);

namespace App\Services;

use Illuminate\Console\OutputStyle;
use Illuminate\Support\{
    Carbon,
    Collection,
    Facades\File,
    Facades\Http,
    Facades\Log,
    Str
};
use Phar;
use RuntimeException;
use Throwable;

class Importer
{
    private Collection $dataCollection;
    private ?OutputStyle $output;

    public function __construct(OutputStyle $output = null)
    {
        $this->dataCollection = collect();
        $this->output = $output;
    }

    public function import(bool $fromCache): void
    {
        $data = $this->loadRawData($fromCache);
        $this->dataCollection = $this->parseData($data);
    }

    private function getDataUrl(bool $fromCache): string
    {
        if (Phar::running()) {
            return ($fromCache)
                ? 'phar://' . getcwd() . '/besel/database/' . config('besel.data_cache_filename')
                : config('besel.data_url');
        }

        return ($fromCache)
            ? getcwd() . '/database/' . config('besel.data_cache_filename')
            : config('besel.data_url');
    }

    private function parseRow(array $row): array
    {
        try {
            $row[0] = (int)Str::after($row[0], '<br>');
            $row[1] = (int)$row[1];
            $row[2] = (int)$row[2];
            $row[3] = (int)$row[3];

            $row[4] = Carbon::parse($row[4])->toDateString();
            $row[5] = Carbon::parse($row[5])->toDateString();
        } catch (Throwable $throwable) {
            $error = 'Error parsing data: ' . $throwable->getMessage();
            Log::error($error, ['row' => $row]);
            throw new RuntimeException($error);
        }

        return $row;
    }

    private function loadFromSite(string $dataUrl): string
    {
        $response = Http::get($dataUrl);

        if ($response->failed()) {
            $error = 'Can not retrieve data from site.';
            Log::error($error, [
                'clientError' => $response->clientError(),
                'serverError' => $response->serverError(),
            ]);
            throw new RuntimeException($error, $response->status());
        }

        return $response->body();
    }

    public function getDataCollection(): Collection
    {
        return $this->dataCollection;
    }

    private function loadRawData(bool $fromCache): string
    {
        $dataUrl = $this->getDataUrl($fromCache);

        $this->output->writeln("Use source for input: <href={$dataUrl}>{$dataUrl}</>");

        if ($fromCache) {
            if (!File::exists($dataUrl) || (File::size($dataUrl) === 0)) {
                throw new RuntimeException("No data in Cache file to work with.");
            }
        }

        $data = ($fromCache) ? File::get($dataUrl) : $this->loadFromSite($dataUrl);
        $dataLength = Str::length($data);

        if ($dataLength === 0) {
            throw new RuntimeException("No data to work with (0 byte received).");
        }

        if ($dataLength > 0) {
            $this->output->writeln("Received <info>{$dataLength}</info> byte(s).");
        }

        return $data;
    }

    private function parseData(string $data): Collection
    {
        $dataArray = explode("\n", $data);

        $this->output->writeln("Normalize header (make snake_cased).");

        $header = array_map(
            static fn($item) => Str::snake(Str::replaceLast('ID', 'Id', $item)),
            explode(',', array_shift($dataArray))
        );

        $totalRows = count($dataArray);

        $headerSize = count($header);
        $dataCollection = collect();

        $this->output->writeln("There are <info>{$totalRows}</info> rows in received data (wo header). Parsing...");

        $this->output->newLine();
        $bar = $this->output->createProgressBar($totalRows);
        $bar->start();

        $emptyRows = 0;

        collect($dataArray)->each(function ($item, $key) use ($header, $headerSize, &$dataCollection, $totalRows, $bar, &$emptyRows) {
            $itemArray = explode(',', $item);
            if (($itemArray === false) || (count($itemArray) !== $headerSize)) {
                if ($item === "") {
                    $emptyRows++;
                }

                Log::warning("Row not parsed.", [
                    'row' => $item,
                    'index' => $key,
                    'total' => $totalRows,
                ]);
                return;
            }
            $dataCollection->push(array_combine(
                    $header,
                    $this->parseRow($itemArray))
            );
            $bar->advance();
        });
        $bar->finish();
        $this->output->newLine(2);

        $parsed = $dataCollection->count();

        if ($parsed <= $totalRows) {
            $this->output->writeln("Successfully parsed <info>{$parsed}</info> of <error>{$totalRows}</error> rows (<comment>{$emptyRows}</comment> empty row(s)). See log for details.");
        }

        return $dataCollection;
    }
}
