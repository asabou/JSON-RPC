<?php

namespace Students\Dao\Repo;

use Doctrine\ORM\EntityManager;
use Students\Dao\Entity\SchoolObjectEntity;

class SchoolObjectRepo {
    private EntityManager $em;

    public function __construct(private DoctrineUtil $doctrineUtil) {
        $this->em = $this->doctrineUtil->getEntityManager();
    }

    public function save(SchoolObjectEntity $schoolObject) {
        $this->em->persist($schoolObject);
        $this->em->flush();
    }

    public function update(SchoolObjectEntity $schoolObjectEntity) {
        $this->em->flush();
    }

    public function delete(SchoolObjectEntity $schoolObjectEntity) {
        $this->em->remove($schoolObjectEntity);
        $this->em->flush();
    }

    public function findAll() {
        $queryBuilder = $this->em->createQueryBuilder();
        return $queryBuilder
            ->select('so')
            ->from(SchoolObjectEntity::class, 'so')
            ->getQuery()
            ->getResult();
    }

    public function findByID(string $id) {
        return $this->em->find(SchoolObjectEntity::class, $id);
    }

    public function deleteAll() {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder
            ->delete(SchoolObjectEntity::class)
            ->getQuery()
            ->execute();
    }
}