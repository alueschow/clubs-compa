<?php

namespace App\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;


class BaseRepository implements ObjectRepository
{
    private $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    /**
     * Emulate findAll() method from EntityRepository
     * @param $table
     * @return array
     */
    public function findAllBase($table) {
        return $this->em->createQueryBuilder()
            ->select("x")
            ->from($table, 'x')
            ->getQuery()
            ->getResult();
    }

    /**
     * Emulate find() method from EntityRepository
     * @param $table
     * @param $column
     * @param $id
     * @return array
     */
    public function findBase($table, $column, $id) {
        return $this->em->createQueryBuilder()
            ->select("x")
            ->from($table, 'x')
            ->where("x." . $column . "=" . $id)
            ->getQuery()
            ->getResult();
    }


    // Methods from implemented ObjectRepository;
    // needed for update to Symfony 5;
    // currently not used
    /**
     * @inheritDoc
     */
    public function find($id)
    {
        // TODO: Implement find() method.
    }

    /**
     * @inheritDoc
     */
    public function findAll()
    {
        // TODO: Implement findAll() method.
    }

    /**
     * @inheritDoc
     */
    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
    {
        // TODO: Implement findBy() method.
    }

    /**
     * @inheritDoc
     */
    public function findOneBy(array $criteria)
    {
        // TODO: Implement findOneBy() method.
    }

    /**
     * @inheritDoc
     */
    public function getClassName()
    {
        // TODO: Implement getClassName() method.
    }
}