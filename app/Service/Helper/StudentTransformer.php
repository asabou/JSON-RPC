<?php

namespace Students\Service\Helper;

use Students\Dao\Entity\StudentEntity;
use Students\Service\Model\StudentDTO;

class StudentTransformer {
    public static function fillStudentDTO(StudentEntity $input, StudentDTO $target) {
        $target->setName($input->getName());
        $target->setGroup($input->getGr());
    }

    public static function fillStudentEntity(StudentDTO $input, StudentEntity $target) {
        $target->setName($input->getName());
        $target->setGr($input->getGroup());
    }

    public static function transformStudentEntity(StudentEntity $input): StudentDTO {
        $target = new StudentDTO();
        self::fillStudentDTO($input, $target);
        return $target;
    }

    public static function transformStudentDTO(StudentDTO $input): StudentEntity {
        $target = new StudentEntity();
        self::fillStudentEntity($input, $target);
        return $target;
    }

    public static function transformStudentEntities(array $inputs): array {
        $targets = array();
        foreach ($inputs as $input) {
            $targets[] = self::transformStudentEntity($input);
        }
        return $targets;
    }
}