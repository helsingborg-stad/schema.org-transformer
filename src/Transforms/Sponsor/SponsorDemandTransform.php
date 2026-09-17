<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms;

use Municipio\Schema\Schema;

class SponsorDemandTransform extends SponsorBaseTransform
{
    public function __construct(string $idprefix)
    {
        parent::__construct($idprefix);
    }

    public function transform(array $data): array
    {
        $demands = [];
        foreach ($data ?? [] as &$row) {
            $acf = $row['acf'] ?? [];

            $event = $this->transformEvent($acf)
                ->name($row['title']['rendered'] ?? '')
                ->image($this->transformImage($acf))
                ->location($this->transformLocation($acf))
                ->hasSponsorshipOffer($this->transformOffer($acf))
                ->keywords($this->transformKeywords('activities', $acf))
                ->organizer($this->transformOrganization($acf)
                    ->contactPoint($this->transformContactPoint($acf))
                    -> keywords([
                    Schema::definedTerm()
                        ->name($acf['organization_eligible_for_grants'])
                        ->inDefinedTermSet(Schema::definedTermSet()->name('organization_eligible_for_grants'))
                ]));

            $demands[] = $event->toArray();
        }

        return $demands;
    }
}
