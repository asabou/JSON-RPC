<?php

namespace Students\Service\Impl;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Students\Dao\Entity\GradeEntity;
use Students\Dao\Entity\SchoolObjectEntity;
use Students\Dao\Repo\DoctrineUtil;
use Students\Dao\Repo\GradeRepo;
use Students\Dao\Repo\SchoolObjectRepo;
use Students\Dao\Repo\StudentRepo;
use Students\Service\Abstracts\AbstractService;
use Students\Service\Helper\SchoolObjectTransformer;
use Students\Service\Model\SchoolObjectDTO;

class SchoolObjectService extends AbstractService {
    private Logger $log;

    public function __construct(
        SchoolObjectRepo $schoolObjectRepo,
        GradeRepo $gradeRepo,
        StudentRepo $studentRepo,
        DoctrineUtil $doctrineUtil
    ) {
        parent::__construct($studentRepo, $schoolObjectRepo, $gradeRepo, $doctrineUtil);
        $this->log = new Logger("SchoolObjectService");
        $logStream = new StreamHandler("..\\php.log");
        $this->log->pushHandler($logStream);
    }

    public function addSchoolObject(SchoolObjectDTO $schoolObjectDTO) {
        $this->log->info("Trying to create a new school object " . $schoolObjectDTO->getName());
        $s = $this->schoolObjectRepo->findByID($schoolObjectDTO->getName());
        if ($s == null) {
            $schoolObjectToSave = SchoolObjectTransformer::transformSchoolObjectDTO($schoolObjectDTO);
            $this->schoolObjectRepo->save($schoolObjectToSave);
            $this->log->info("School object saved");
        } else {
            $this->log->error("The school object " . $schoolObjectDTO->getName() . " already exists");
        }
    }

    public function updateSchoolObject(SchoolObjectDTO $schoolObjectDTO) {
        $this->log->info("Trying to update school object " . $schoolObjectDTO->getName());
        $s = $this->schoolObjectRepo->findByID($schoolObjectDTO->getName());
        if ($s != null) {
            SchoolObjectTransformer::fillSchoolObjectEntity($schoolObjectDTO, $s);
            $this->schoolObjectRepo->update($s);
        } else {
            $this->log->error("The school object " . $schoolObjectDTO->getName() . " does not exists");
        }
    }

    public function deleteSchoolObject(string $id) {
        $this->log->info("Trying to delete school object " . $id);
        $s = $this->schoolObjectRepo->findByID($id);
        if ($s != null) {
            try {
                //todo: work with detach entities (I think it is the best practice)
                $this->em->beginTransaction();
                $queryBuilder = $this->em->createQueryBuilder();
                $queryBuilder
                    ->delete(GradeEntity::class, 'g')
                    ->where('identity(g.schoolObject) = :schoolObject')
                    ->setParameter("schoolObject", $id)
                    ->getQuery()
                    ->execute();
                $queryBuilder
                    ->delete(SchoolObjectEntity::class, 'g')
                    ->where('g.name = :schoolObject')
                    ->setParameter("schoolObject", $id)
                    ->getQuery()
                    ->execute();
                $this->log->info("Grades delete");
                $this->em->commit();
            } catch (\Exception) {
                $this->log->error("Cannot delete SchoolObject");
                $this->em->rollback();
            }
        } else {
            $this->log->error("School object to delete " . $id . " does not exists");
        }
    }

    public function getAllSchoolObjects() {
        $this->log->info("Trying to find all school objects ...");
        $schoolObjects = $this->schoolObjectRepo->findAll();
        $this->log->info("School objects founf " . count($schoolObjects));
        return SchoolObjectTransformer::transformScholObjectEntities($schoolObjects);
    }

    public function deleteAll() {
        $this->log->info("Trying to delete all school objects");
        $this->schoolObjectRepo->deleteAll();
    }

}