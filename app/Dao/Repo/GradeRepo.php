<?php

namespace Students\Dao\Repo;

use Doctrine\ORM\EntityManager;
use Students\Dao\Entity\GradeEntity;

class GradeRepo {
    private EntityManager $em;

    public function __construct(private DoctrineUtil $doctrineUtil) {
        $this->em = $this->doctrineUtil->getEntityManager();
    }

    public function save(GradeEntity $grade) {
        $schoolObject = $this->em->merge($grade->getSchoolObject());
        $student = $this->em->merge($grade->getStudent());
        $grade->setSchoolObject($schoolObject);
        $grade->setStudent($student);
        $this->em->persist($grade);
        $this->em->flush();
    }

    public function getCatalogForStudent(string $student) {
        $queryBuilder = $this->em->createQueryBuilder();
        return $queryBuilder
            ->select('identity(g.student) as student_name_id', 'identity(g.schoolObject) as school_object_id', 'g.value')
            ->from(GradeEntity::class, 'g')
            ->where('identity(g.student) = :student')
            ->setParameter('student', $student)
            ->orderBy('identity(g.schoolObject)')
            ->getQuery()
            ->getResult();
    }

    public function getCatalogForSchoolObject(string $schoolObject) {
        $queryBuilder = $this->em->createQueryBuilder();
        return $queryBuilder
            ->select('identity(g.student) as student_name_id', 'identity(g.schoolObject) as school_object_id', 'g.value')
            ->from(GradeEntity::class, 'g')
            ->where('identity(g.schoolObject) = :schoolObject')
            ->setParameter('schoolObject', $schoolObject)
            ->orderBy('identity(g.student)')
            ->getQuery()
            ->getResult();
    }

    public function findByID(string $student, string $schoolObject) {
        $queryBuilder = $this->em->createQueryBuilder();
        $grades = $queryBuilder
            ->select('identity(g.student) as student_name_id', 'identity(g.schoolObject) as school_object_id', 'g.value')
            ->from(GradeEntity::class, 'g')
            ->where('identity(g.student) = :student')
            ->andWhere('identity(g.schoolObject) = :schoolObject')
            ->setParameter('schoolObject', $schoolObject)
            ->setParameter('student', $student)
            ->getQuery()
            ->getResult();
        if (count($grades) > 0) {
            return $grades[0];
        }
        return null;
    }

    public function update(GradeEntity $grade) {
        $this->em->merge($grade);
        $this->em->flush();
    }

    public function findAll() {
        $queryBuilder = $this->em->createQueryBuilder();
        return $queryBuilder
            ->select('identity(g.student) as student_name_id', 'identity(g.schoolObject) as school_object_id', 'g.value')
            ->from(GradeEntity::class, 'g')
            ->getQuery()
            ->getResult();
    }

    public function deleteAll() {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder
            ->delete(GradeEntity::class)
            ->getQuery()
            ->execute();
    }
}