<?php

namespace App\Services\Import;

use App\Entity\AssessmentModule\Assessment;


class AssessmentImport extends AbstractImport
{
    public static function deleteAssessmentTable()
    {
        $qb = self::$em->createQueryBuilder();
        $qb->delete(Assessment::class, 'a')
            ->getQuery()
            ->getResult();
    }

}
