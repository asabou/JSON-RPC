<?php

namespace Students\Service\Model;

class SchoolObjectDTO {
    private string $name;
    private string $teacher;

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function getTeacher(): string {
        return $this->teacher;
    }

    public function setTeacher(string $teacher): void {
        $this->teacher = $teacher;
    }
}