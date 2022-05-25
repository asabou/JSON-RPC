<?php

namespace Students\Service\Helper;

use Students\Dao\Entity\GradeEntity;
use Students\Service\Model\GradeDTO;

class GradeTransformer {
    public static function fillGradeDTO(GradeEntity $input, GradeDTO $target) {
        $target->setStudent(StudentTransformer::transformStudentEntity($input->getStudent()));
        $target->setSchoolObject(SchoolObjectTransformer::transformSchoolObjectEntity($input->getSchoolObject()));
        $target->setValue($input->getValue());
    }

    public static function fillGradeEntity(GradeDTO $input, GradeEntity $target) {
        $target->setStudent(StudentTransformer::transformStudentDTO($input->getStudent()));
        $target->setSchoolObject(SchoolObjectTransformer::transformSchoolObjectDTO($input->getSchoolObject()));
        $target->setValue($input->getValue());
    }

    public static function transformGradeEntity(GradeEntity $input): GradeDTO {
        $target = new GradeDTO();
        self::fillGradeDTO($input, $target);
        return $target;
    }

    public static function transformGradeDTO(GradeDTO $input): GradeEntity {
        $target = new GradeEntity();
        self::fillGradeEntity($input, $target);
        return $target;
    }

    public static function transformGradeEntities(array $inputs): array {
        $targets = array();
        foreach ($inputs as $input) {
            $targets[] = self::transformGradeEntity($input);
        }
        return $targets;
    }
}