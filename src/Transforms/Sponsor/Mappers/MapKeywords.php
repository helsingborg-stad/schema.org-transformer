<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Mappers;

use Municipio\Schema\Schema;

class MapKeywords extends AbstractSponsorMapper
{
    public function createKeyword(array $data, string $name): ?\Municipio\Schema\DefinedTerm
    {
        return $this->withAcfFields($data, fn (array $acf) =>
            Schema::definedTerm()
                ->name($acf[$name] ?? null)
                ->inDefinedTermSet(Schema::definedTermSet()->name($name ?? null)), null);
    }

    public function map(array $data): ?array
    {
            return $this->withAcfFields(
                $data,
                fn(array $acf) => array_map(
                    fn($row) => Schema::definedTerm()
                    ->name($row['name'] ?? null)
                    ->inDefinedTermSet(Schema::definedTermSet()->name($row['taxonomy'] ?? null)),
                    $acf
                ),
                'activities'
            );
    }
}
