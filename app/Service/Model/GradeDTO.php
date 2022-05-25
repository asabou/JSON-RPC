<?php

namespace Students\Service\Model;

class GradeDTO {
    private StudentDTO $student;
    private SchoolObjectDTO $schoolObject;
    private int $value;

    public function getStudent(): StudentDTO {
        return $this->student;
    }

    public function setStudent(StudentDTO $student): void {
        $this->student = $student;
    }

    public function getSchoolObject(): SchoolObjectDTO {
        return $this->schoolObject;
    }

    public function setSchoolObject(SchoolObjectDTO $schoolObject): void {
        $this->schoolObject = $schoolObject;
    }

    public function getValue(): int {
        return $this->value;
    }

    public function setValue(int $value): void {
        $this->value = $value;
    }
}