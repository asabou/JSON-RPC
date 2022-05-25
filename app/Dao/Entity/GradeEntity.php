<?php

namespace Students\Dao\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table('grades')]
class GradeEntity {
    #[Id]
    #[ManyToOne(targetEntity: StudentEntity::class, cascade: ['merge', 'detach'])]
    #[JoinColumn(name: 'student_name_id', referencedColumnName: 'name')]
    private StudentEntity $student;

    #[Id]
    #[ManyToOne(targetEntity: SchoolObjectEntity::class, cascade: ['merge', 'detach'])]
    #[JoinColumn(name: 'school_object_id', referencedColumnName: 'name')]
    private SchoolObjectEntity $schoolObject;

    #[Column(name: 'value')]
    private int $value;

    public function getStudent(): StudentEntity {
        return $this->student;
    }

    public function getSchoolObject(): SchoolObjectEntity {
        return $this->schoolObject;
    }

    public function getValue(): int {
        return $this->value;
    }

    public function setStudent(StudentEntity $student) {
        $this->student = $student;
    }

    public function setSchoolObject(SchoolObjectEntity $schoolObject) {
        $this->schoolObject = $schoolObject;
    }

    public function setValue(int $value) {
        $this->value = $value;
    }
}