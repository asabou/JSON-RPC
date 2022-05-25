<?php

namespace Students\Service\Helper;

use Students\Dao\Entity\SchoolObjectEntity;
use Students\Service\Model\SchoolObjectDTO;

class SchoolObjectTransformer {
    public static function fillSchoolObjectDTO(SchoolObjectEntity $input, SchoolObjectDTO $target) {
        $target->setName($input->getName());
        $target->setTeacher($input->getTeacher());
    }

    public static function fillSchoolObjectEntity(SchoolObjectDTO $input, SchoolObjectEntity $target) {
        $target->setName($input->getName());
        $target->setTeacher($input->getTeacher());
    }

    public static function transformSchoolObjectEntity(SchoolObjectEntity $input): SchoolObjectDTO {
        $target = new SchoolObjectDTO();
        self::fillSchoolObjectDTO($input, $target);
        return $target;
    }

    public static function transformSchoolObjectDTO(SchoolObjectDTO $input): SchoolObjectEntity {
        $target = new SchoolObjectEntity();
        self::fillSchoolObjectEntity($input, $target);
        return $target;
    }

    public static function transformScholObjectEntities(array $inputs): array {
        $targets = array();
        foreach ($inputs as $input) {
            $targets[] = self::transformSchoolObjectEntity($input);
        }
        return $targets;
    }
}