<?php

namespace Students\Dao\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table('students')]
class StudentEntity {
    #[Id]
    #[Column(name: 'name')]
    private string $name;

    #[Column(name: 'gr')]
    private string $gr;

    public function getName(): string {
        return $this->name;
    }

    public function getGr(): string {
        return $this->gr;
    }

    public function setName(string $name) {
        $this->name = $name;
    }

    public function setGr(string $gr) {
        $this->gr = $gr;
    }
}