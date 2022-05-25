<?php

declare(strict_types = 1);

namespace Students\Dao\Repo;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Dotenv\Dotenv;

class DoctrineUtil {

    private EntityManager $entityManager;

    public function __construct() {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 3));
        $dotenv->load();
        $params = [
            "dbname" => $_ENV["DB_DATABASE"],
            "user"   => $_ENV["DB_USER"],
            "password" => $_ENV["DB_PASS"],
            "host"  => $_ENV["DB_HOST"],
            "driver" => $_ENV["DB_DRIVER"],
            "databaseOptions" => $_ENV["DB_OPTIONS"]
        ];
        $config = Setup::createAttributeMetadataConfiguration([__DIR__ . "..\\Entity"]);
        $this->entityManager = EntityManager::create($params, $config);
    }

    public function getEntityManager(): EntityManager {
        return $this->entityManager;
    }
}