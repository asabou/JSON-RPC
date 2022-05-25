<?php

namespace Students\Service\Abstracts;

use Doctrine\ORM\EntityManager;
use Students\Dao\Entity\GradeEntity;
use Students\Dao\Repo\DoctrineUtil;
use Students\Dao\Repo\GradeRepo;
use Students\Dao\Repo\SchoolObjectRepo;
use Students\Dao\Repo\StudentRepo;

abstract class AbstractService {
    protected EntityManager $em;

    public function __construct(
        protected StudentRepo $studentRepo,
        protected SchoolObjectRepo $schoolObjectRepo,
        protected GradeRepo $gradeRepo,
        protected DoctrineUtil $doctrineUtil
    ) {
        $this->em = $this->doctrineUtil->getEntityManager();
    }

    protected function convertMatrixToListOfObjects(array $matrix): array {
        $list = array();
        foreach ($matrix as $line) {
            $list[] = $this->convertLineToObject($line);
        }
        return $list;
    }

    protected function convertLineToObject(array $line): GradeEntity {
        $obj = new GradeEntity();
        $obj->setStudent($this->studentRepo->findByID($line["student_name_id"]));
        $obj->setSchoolObject($this->schoolObjectRepo->findByID($line["school_object_id"]));
        $obj->setValue($line["value"]);
        return $obj;
    }

}