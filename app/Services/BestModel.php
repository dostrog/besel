<?php
declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class BestModel
{
    public function execute($buyerId)
    {
        if ($buyerId === null ) {
            $filter = ' ';
        } elseif (! is_numeric($buyerId)) {
            throw new InvalidArgumentException("Invalid ID for Buyer");
        } else {
            $filter = " where buyer_id = {$buyerId} ";
        }

        $sql = sprintf($this->prepareRawSql(), $filter);

        $result = DB::select(DB::raw($sql)->getValue());

        $quantity = 1;
        return array_map(static function($record) use (&$quantity){
            return array_merge([$quantity++], array_values((array)$record));
        }, $result);

    }

    private function prepareRawSql(): string
    {
        return <<<'RAWSQL'
            select model_id, cnt, buyer_id, name, surname
            from (
                     select
                         model_id,
                         count(model_id) as cnt,
                         buyer_id,
                         row_number() over (partition by buyer_id order by count(model_id) desc) as best
                     from trades
                     %s
                     group by
                         model_id,
                         buyer_id
                 ) as rang, buyers
            where best = 1 and buyer_id = buyers.id
            order by cnt desc
        RAWSQL;
    }
}
