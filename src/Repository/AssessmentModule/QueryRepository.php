<?php

namespace App\Repository\AssessmentModule;

use App\Entity\AssessmentModule\Query;
use Doctrine\ORM\EntityManagerInterface;


class QueryRepository extends BaseAssessmentRepository
{
    private $em;

    public function __construct(EntityManagerInterface $em) {
        parent::__construct($em);
        $this->em = $em;
    }

    public function findAll() {
        return parent::findAllBase(Query::class);
    }

    public function find($id) {
        return parent::findBase(Query::class, 'query_id', $id);
    }

}