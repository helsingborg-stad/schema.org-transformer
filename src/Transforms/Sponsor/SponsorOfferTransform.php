<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms;

use Municipio\Schema\Schema;

class SponsorOfferTransform extends SponsorBaseTransform
{
    public function __construct(string $idprefix)
    {
        parent::__construct($idprefix);
    }
    public function transform(array $data): array
    {
        $offers = [];
        foreach ($data ?? [] as &$row) {
            $acf = $row['acf'] ?? [];

            $offer = $this->transformOffer($acf)
                ->identifier($row['id'] ?? '')
                ->name($row['title']['rendered'] ?? '')
                ->image($this->transformImage($acf))
                ->location($this->transformLocation($acf))
                ->keywords($this->transformKeywords('activities', $acf))
                ->offeredBy($this->transformOrganization($acf)
                    ->contactPoint($this->transformContactPoint($acf))
                ->demand($this->transformDemand($acf)
                ->keywords([
                    Schema::definedTerm()
                        ->name($acf['proposal_for_counter_performance'])
                        ->inDefinedTermSet(Schema::definedTermSet()->name('proposal_for_counter_performance'))
                ])));

            $offers[] = $offer->toArray();
        }

        return $offers;
    }
}
