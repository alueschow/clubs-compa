<?php

namespace App\Services\Import\ComparisonModule;

use App\Entity\ComparisonModule\Comparison;
use App\Services\Import\AbstractImport;


class ComparisonImport extends AbstractImport
{
    public static function deleteComparisonTable()
    {
        $qb = self::$em->createQueryBuilder();
        $qb->delete(Comparison::class, 'a')
            ->getQuery()
            ->getResult();
    }

}
