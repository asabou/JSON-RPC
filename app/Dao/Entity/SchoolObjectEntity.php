<?php

namespace Students\Dao\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table('school_objects')]
class SchoolObjectEntity {
    #[Id]
    #[Column(name: 'name')]
    private string $name;

    #[Column(name: 'teacher')]
    private string $teacher;

    public function getName(): string {
        return $this->name;
    }

    public function getTeacher(): string {
        return $this->teacher;
    }

    public function setName(string $name) {
        $this->name = $name;
    }

    public function setTeacher(string $teacher) {
        $this->teacher = $teacher;
    }
}