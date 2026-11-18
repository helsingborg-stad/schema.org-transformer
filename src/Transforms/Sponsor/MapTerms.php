<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor;

use Municipio\Schema\Schema;

class MapTerms
{
    public static function fromRows(array|false|null $rows, string $group): ?array
    {
        $terms = [];
        foreach ($rows ?: [] as $row) {
            $name = trim($row['name'] ?? '');
            if ($name !== '') {
                $terms[] = Schema::definedTerm()->name($name)
                    ->inDefinedTermSet(Schema::definedTermSet()->name($group));
            }
        }

        return $terms ?: null;
    }
}
