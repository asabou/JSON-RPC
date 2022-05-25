<?php

namespace Students\Service\Model;

class StudentDTO {
    private string $name;
    private string $group;

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function getGroup(): string {
        return $this->group;
    }

    public function setGroup(string $group): void {
        $this->group = $group;
    }
}