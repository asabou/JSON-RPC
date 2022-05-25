<?php

namespace Students\Service\Impl;

use Doctrine\ORM\EntityManager;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Students\Dao\Entity\GradeEntity;
use Students\Dao\Entity\StudentEntity;
use Students\Dao\Repo\DoctrineUtil;
use Students\Dao\Repo\GradeRepo;
use Students\Dao\Repo\SchoolObjectRepo;
use Students\Dao\Repo\StudentRepo;
use Students\Service\Abstracts\AbstractService;
use Students\Service\Helper\StudentTransformer;
use Students\Service\Model\StudentDTO;

class StudentService extends AbstractService {
    private Logger $log;

    public function __construct(
        StudentRepo $studentRepo,
        SchoolObjectRepo $schoolObjectRepo,
        GradeRepo $gradeRepo,
        DoctrineUtil $doctrineUtil
    ) {
        parent::__construct($studentRepo, $schoolObjectRepo, $gradeRepo, $doctrineUtil);
        $this->log = new Logger("StudentService");
        $logStream = new StreamHandler("..\\php.log");
        $this->log->pushHandler($logStream);
    }

    public function addStudent(StudentDTO $studentDTO) {
        $this->log->info("Trying to create a new student " . $studentDTO->getName() . " ...");
        $s = $this->studentRepo->findByID($studentDTO->getName());
        if ($s == null) {
            $studentToSave = StudentTransformer::transformStudentDTO($studentDTO);
            $this->studentRepo->save($studentToSave);
            $this->log->info("Student saved ..");
        } else {
            $this->log->error("This student " . $studentDTO->getName() . " already exists!");
        }
    }

    public function updateStudent(StudentDTO $studentDTO) {
        $this->log->info("Trying to update student " . $studentDTO->getName());
        $s = $this->studentRepo->findByID($studentDTO->getName());
        if ($s != null) {
            StudentTransformer::fillStudentEntity($studentDTO, $s);
            $this->studentRepo->update($s);
            $this->log->info("Student updated");
        } else {
            $this->log->error("Student to update does not exists!");
        }
    }

    public function deleteStudent(string $id) {
        $this->log->info("Trying to delete student " . $id);
        $s = $this->studentRepo->findByID($id);
        if ($s != null) {
            try {
                //todo: work with detach entities (I think it is the best practice)
                $this->em->beginTransaction();
                $queryBuilder = $this->em->createQueryBuilder();
                $queryBuilder
                    ->delete(GradeEntity::class, 'g')
                    ->where('identity(g.student) = :student')
                    ->setParameter("student", $id)
                    ->getQuery()
                    ->execute();
                $queryBuilder
                    ->delete(StudentEntity::class, 'g')
                    ->where('g.name = :student')
                    ->setParameter("student", $id)
                    ->getQuery()
                    ->execute();
                $this->log->info("Grades delete");
                $this->em->commit();
            } catch (\Exception $e) {
                $this->log->error($e->getMessage());
                $this->log->error("Cannot delete the student");
                $this->em->rollback();
            }
        } else {
            $this->log->error("Student to delete does not exists!");
        }
    }

    public function getAllStudents(): array {
        $this->log->info("Trying to retrieve all students ...");
        $students = $this->studentRepo->findAll();
        $this->log->info("Students found: " . count($students));
        return StudentTransformer::transformStudentEntities($students);
    }

    public function findByID(string $id) {
        return $this->studentRepo->findByID($id);
    }

    public function deleteAll() {
        $this->log->info("Trying to delete all students ...");
        $this->studentRepo->deleteAll();
    }
}