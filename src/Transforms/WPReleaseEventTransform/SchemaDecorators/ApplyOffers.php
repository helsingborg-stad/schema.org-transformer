<?php

namespace SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators;

use SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorator;
use Municipio\Schema\BaseType;
use Municipio\Schema\Schema;

class ApplyOffers implements SchemaDecorator
{
    public function apply(BaseType $event, array $data): BaseType
    {
        if ($this->isFreeEvent($data) || !$this->hasPricesList($data)) {
            return $event;
        }

        return $event->setProperty('offers', $this->createOffers($data));
    }

    private function isFreeEvent(array $data): bool
    {
        return empty($data['acf']['pricing']) || $data['acf']['pricing'] !== 'expense';
    }

    private function hasPricesList(array $data): bool
    {
        return !empty($data['acf']['pricesList']);
    }

    private function createOffers($data): array
    {
        return array_map(function ($priceRow) {
            return Schema::offer()
            ->price($priceRow['price'] ?? null)
            ->name($priceRow['priceLabel'] ?? null)
            ->priceCurrency('SEK');
        }, $data['acf']['pricesList']);
    }
}
