<?php

namespace App\Services\Import;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;


abstract class AbstractImport
{
    /** @var EntityManager em */
    protected static $em;
    protected static $header;
    protected static $import_data;


    public static function setEntityManager($em)
    {
        self::$em = $em;
    }

    public static function setHeader($header)
    {
        self::$header = $header;
    }

    public static function setImportData($data)
    {
        self::$import_data = $data;
    }

    protected static function createNewEntityManager()
    {
        $new_em = null;
        try {
            $new_em = self::$em->create(
                self::$em->getConnection(),
                self::$em->getConfiguration(),
                self::$em->getEventManager()
            );
        } catch (ORMException $exception) {
            //
        }
        return $new_em;
    }

}
