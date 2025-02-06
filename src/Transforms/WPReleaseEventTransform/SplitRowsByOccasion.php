<?php

namespace SchemaTransformer\Transforms;

use SchemaTransformer\Interfaces\AbstractDataTransform;

class SplitRowsByOccasion implements AbstractDataTransform
{
    public function transform($data): array
    {
        $rowsWithSingleOccasion = [];

        foreach ($data as $rowWithMultipleOccasions) {
            if (empty($rowWithMultipleOccasions['acf']['occasions'])) {
                continue;
            }

            if (count($rowWithMultipleOccasions['acf']['occasions']) === 1) {
                $rowsWithSingleOccasion[] = $rowWithMultipleOccasions;
                continue;
            }

            foreach ($rowWithMultipleOccasions['acf']['occasions'] as $i => $occasion) {
                $rowWithSingleOccasion                     = $rowWithMultipleOccasions;
                $rowWithSingleOccasion['acf']['occasions'] = [$occasion];
                $rowWithSingleOccasion['id']               = $rowWithSingleOccasion['id'] . '-' . $i;
                $rowsWithSingleOccasion[]                  = $rowWithSingleOccasion;
            }
        }

        return $rowsWithSingleOccasion;
    }
}
