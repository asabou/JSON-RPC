<?php

declare(strict_types = 1);

namespace Students\Dao\Repo;

use Doctrine\ORM\EntityManager;
use Students\Dao\Entity\StudentEntity;

class StudentRepo {
    private EntityManager $em;

    public function __construct(private DoctrineUtil $doctrineUtil) {
        $this->em = $this->doctrineUtil->getEntityManager();
    }

    public function save(StudentEntity $student) {
        $this->em->persist($student);
        $this->em->flush();
    }

    public function update(StudentEntity $student) {
        $this->em->flush();
    }

    public function delete(StudentEntity $student) {
        $this->em->remove($student);
        $this->em->flush();
    }

    public function findAll() {
        $queryBuilder = $this->em->createQueryBuilder();
        return $queryBuilder
            ->select('s')
            ->from(StudentEntity::class, 's')
            ->getQuery()
            ->getResult();
    }

    public function findByID(string $id) {
        return $this->em->find(StudentEntity::class, $id);
    }

    public function deleteAll() {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder
            ->delete(StudentEntity::class)
            ->getQuery()
            ->execute();
    }
}