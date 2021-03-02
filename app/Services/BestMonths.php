<?php
declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;

class BestMonths
{
    public function execute($modelId, $period = 3): array
    {
        if (DB::getDefaultConnection() === 'sqlite') {
            $dataUrl = config('database.connections.sqlite.database');

            if (!File::exists($dataUrl) || (File::size($dataUrl) === 0)) {
                throw new RuntimeException("No data in Cache file to work with.");
            }

            $result = DB::select(DB::raw($this->prepareRawSqlSqlite($modelId, $period))->getValue());
        } else {
            $result = DB::select(DB::raw($this->prepareRawSql())->getValue(), [$modelId, $period]);
        }
//
//
//        $result = (DB::getDefaultConnection() === 'sqlite')
//            ? DB::select(DB::raw($this->prepareRawSqlSqlite($modelId, $period))->getValue())
//            : DB::select(DB::raw($this->prepareRawSql())->getValue(), [$modelId, $period]);

        return array_map(static function($record){
            return array_values((array)$record);
        }, $result);
    }

    private function prepareRawSql(): string
    {
        return <<<"RAWSQL"
            select model_id, cnt, mon, y
            from(
                    select
                        model_id,
                        count(model_id) as cnt,
                        month(sale_date) as mon,
                        year(sale_date) as y,
                        row_number() over (partition by model_id order by count(model_id) desc) as rn
                    from trades
                    where model_id = ?
                    group by
                        model_id, mon, y) as bymon
            where rn<=?
            order by mon
        RAWSQL;
    }

    private function prepareRawSqlSqlite($model_id = 84, $period = 3): string
    {
        return "
            select model_id, cnt, strftime('%m',date(sale_date)) as mon, strftime('%Y',date(sale_date)) as year
            from(
                    select
                        model_id,
                        count(model_id) as cnt,
                        sale_date,
                        row_number() over (partition by model_id order by count(model_id) desc) as rn
                    from trades
                    where model_id = {$model_id}
                    group by
                        model_id, strftime('%m',date(sale_date))) as bymon
            where rn <= ${period}
            order by sale_date";
    }
}
