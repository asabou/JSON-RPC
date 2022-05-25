<?php

namespace Students\Service\Impl;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Students\Dao\Repo\DoctrineUtil;
use Students\Dao\Repo\GradeRepo;
use Students\Dao\Repo\SchoolObjectRepo;
use Students\Dao\Repo\StudentRepo;
use Students\Service\Abstracts\AbstractService;
use Students\Service\Helper\GradeTransformer;
use Students\Service\Helper\SchoolObjectTransformer;
use Students\Service\Helper\StudentTransformer;
use Students\Service\Model\GradeDTO;

class GradeService extends AbstractService {
    private Logger $log;

    public function __construct(
        GradeRepo $gradeRepo,
        StudentRepo $studentRepo,
        SchoolObjectRepo $schoolObjectRepo,
        DoctrineUtil $doctrineUtil
    ) {
        parent::__construct($studentRepo, $schoolObjectRepo, $gradeRepo, $doctrineUtil);
        $this->log = new Logger("GradeService");
        $logStream = new StreamHandler("..\\php.log");
        $this->log->pushHandler($logStream);
    }

    public function addGrade(GradeDTO $gradeDTO) {
        $this->log->info("Trying to add a new grade ...");
        $g = $this->gradeRepo->findByID($gradeDTO->getStudent()->getName(), $gradeDTO->getSchoolObject()->getName());
        if ($g == null) {
            $this->log->info("Grade does not exists and we try to create it ...");
            $student = $this->studentRepo->findByID($gradeDTO->getStudent()->getName());
            $schoolObject = $this->schoolObjectRepo->findByID($gradeDTO->getSchoolObject()->getName());
            $gradeDTO->setStudent(StudentTransformer::transformStudentEntity($student));
            $gradeDTO->setSchoolObject(SchoolObjectTransformer::transformSchoolObjectEntity($schoolObject));
            $gradeToSave = GradeTransformer::transformGradeDTO($gradeDTO);
            $gradeToSave->setSchoolObject($schoolObject);
            $gradeToSave->setStudent($student);
            $this->log->info("Grade before saving " . $gradeToSave->getValue() . " for school object " . $gradeToSave->getSchoolObject()->getName());
            $this->gradeRepo->save($gradeToSave);
            $this->log->info("Grade added");
        } else {
            $this->log->error("Grade to add already exists");
        }
    }

    public function updateGrade(GradeDTO $gradeDTO) {
        $this->log->info("Trying to update grade ...");
        $g = $this->convertLineToObject($this->gradeRepo->findByID($gradeDTO->getStudent()->getName(), $gradeDTO->getSchoolObject()->getName()));
        if ($g != null) {
            GradeTransformer::fillGradeEntity($gradeDTO, $g);
            $this->gradeRepo->update($g);
        } else {
            $this->log->error("Grade to update does not exists");
        }
    }

    public function getCatalogForStudent(string $student) {
        $this->log->info("Trying to get catalog for student " .$student);
        $grades = $this->convertMatrixToListOfObjects($this->gradeRepo->getCatalogForStudent($student));
        $this->log->info("Grades found " . count($grades));
        return GradeTransformer::transformGradeEntities($grades);
    }

    public function getCatalogForSchoolObject(string $schoolObject) {
        $this->log->info("Trying to get catalog for school object " . $schoolObject);
        $grades = $this->convertMatrixToListOfObjects($this->gradeRepo->getCatalogForSchoolObject($schoolObject));
        $this->log->info("Grades found " . count($grades));
        return GradeTransformer::transformGradeEntities($grades);
    }

    public function getAllGrades(): array {
        $this->log->info("Trying to return all grades");
        $grades = $this->convertMatrixToListOfObjects($this->gradeRepo->findAll());
        $this->log->info("Grades found " . count($grades));
        return GradeTransformer::transformGradeEntities($grades);
    }

    public function findByID(string $student, string $schoolObject) {
        $grade = $this->gradeRepo->findByID($student, $schoolObject);
        return $this->convertLineToObject($grade);
    }

    public function deleteAll() {
        $this->log->info("Trying to delete all grades");
        $this->gradeRepo->deleteAll();
    }



}