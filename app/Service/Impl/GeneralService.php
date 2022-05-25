<?php

namespace Students\Service\Impl;

use JetBrains\PhpStorm\Pure;
use Students\Service\Model\GradeDTO;
use Students\Service\Model\SchoolObjectDTO;
use Students\Service\Model\StudentDTO;

class GeneralService {
    public function __construct(
        private StudentService $studentService,
        private SchoolObjectService $schoolObjectService,
        private GradeService $gradeService
    ) { }

    public function cleanupDatabase($params) {
        $this->gradeService->deleteAll();
        $this->schoolObjectService->deleteAll();
        $this->studentService->deleteAll();
    }

    public function addStudent($params) {
        $student = $this->getStudentDTO($params["student"]);
        $this->studentService->addStudent($student);
    }

    public function updateStudent($params) {
        $student = $this->getStudentDTO($params["student"]);
        $this->studentService->updateStudent($student);
    }

    public function deleteStudent($params) {
        $student = $params["student"];
        $this->studentService->deleteStudent($student);
    }

    public function getAllStudents($params) {
        $students = $this->studentService->getAllStudents();
        return $this->convertPHPObjectsToJSON($students);
    }

    public function addSchoolObject($params) {
        $schoolObjectDTO = $this->getSchoolObjectDTO($params["schoolObject"]);
        $this->schoolObjectService->addSchoolObject($schoolObjectDTO);
    }

    public function updateSchoolObject($params) {
        $schoolObjectDTO = $this->getSchoolObjectDTO($params["schoolObject"]);
        $this->schoolObjectService->updateSchoolObject($schoolObjectDTO);
    }

    public function deleteSchoolObject($params) {
        $schoolObject = $params["schoolObject"];
        $this->schoolObjectService->deleteSchoolObject($schoolObject);
    }

    public function getAllSchoolObjects($params): array {
        $schoolObjects = $this->schoolObjectService->getAllSchoolObjects();
        return $this->convertPHPObjectsToJSON($schoolObjects);
    }

    public function addGrade($params) {
        $gradeDTO = $this->getGradeDTO($params["grade"]);
        $this->gradeService->addGrade($gradeDTO);
    }

    public function updateGrade($params) {
        $gradeDTO = $this->getGradeDTO($params["grade"]);
        $this->gradeService->updateGrade($gradeDTO);
    }

    public function getCatalogForStudent($params): array {
        $student = $params["student"];
        $grades = $this->gradeService->getCatalogForStudent($student);
        return $this->convertPHPObjectsToJSON($grades);
    }

    public function getCatalogForSchoolObject($params): array {
        $schoolObject = $params["schoolObject"];
        $grades = $this->gradeService->getCatalogForSchoolObject($schoolObject);
        return $this->convertPHPObjectsToJSON($grades);
    }

    private function convertPHPObjectsToJSON($objects): array {
        $jsons = [];
        foreach ($objects as $object) {
            $objJSON = "";
            if ($object instanceof StudentDTO) {
                $objJSON =  $this->convertStudentDTOToJSON($object);
            }
            if ($object instanceof SchoolObjectDTO) {
                $objJSON = $this->convertSchoolObjectDTOToJSON($object);
            }
            if ($object instanceof GradeDTO) {
                $objJSON = $this->convertGradeDTOToJSON($object);
            }
            $jsons[] = json_decode($objJSON);
        }
        return $jsons;
    }

    #[Pure]
    private function convertStudentDTOToJSON(StudentDTO $student): string {
        return '{"name": "' . $student->getName() . '", "group": "' . $student->getGroup() . '"}';
    }

    #[Pure]
    private function convertSchoolObjectDTOToJSON(SchoolObjectDTO $schoolObject): string {
        return '{"name" : "' . $schoolObject->getName() . '", "teacher" : "' . $schoolObject->getTeacher() . '"}';
    }

    #[Pure]
    private function convertGradeDTOToJSON(GradeDTO $grade): string {
        return '{"student" : ' . $this->convertStudentDTOToJSON($grade->getStudent()) . ', "schoolObject": ' .
            $this->convertSchoolObjectDTOToJSON($grade->getSchoolObject()) . ', "value": "' . $grade->getValue() . '"}';
    }

    public function getGradeDTO($grade): GradeDTO {
        $student = $grade["student"];
        $schoolObject = $grade["schoolObject"];
        $value = $grade["value"];

        $gradeDTO = new GradeDTO();

        $studentDTO = new StudentDTO();
        $studentDTO->setName($student["name"]);

        $schoolObjectDTO = new SchoolObjectDTO();
        $schoolObjectDTO->setName($schoolObject["name"]);

        $gradeDTO->setSchoolObject($schoolObjectDTO);
        $gradeDTO->setStudent($studentDTO);
        $gradeDTO->setValue($value);
        return $gradeDTO;
    }

    private function getStudentDTO($student): StudentDTO {
        $studentDTO = new StudentDTO();
        $studentDTO->setName($student["name"]);
        $studentDTO->setGroup($student["group"]);
        return $studentDTO;
    }

    private function getSchoolObjectDTO($schoolObject): SchoolObjectDTO {
        $schoolObjectDTO = new SchoolObjectDTO();
        $schoolObjectDTO->setName($schoolObject["name"]);
        $schoolObjectDTO->setTeacher($schoolObject["teacher"]);
        return $schoolObjectDTO;
    }
}