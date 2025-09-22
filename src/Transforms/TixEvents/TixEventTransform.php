<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\TixEvents;

use Municipio\Schema\Schema;
use Municipio\Schema\Event;
use SchemaTransformer\Transforms\TransformBase;
use SchemaTransformer\Interfaces\AbstractDataTransform;

class TixEventTransform extends TransformBase implements AbstractDataTransform
{
    public function __construct(string $idprefix)
    {
        parent::__construct($idprefix);
    }

    private function firstNonEmptyArray(...$values)
    {
        foreach ($values as $value) {
            if (is_array($value) && !empty($value)) {
                return $value;
            }
        }
        return [];
    }

    private function getValidDatesFromSource($data)
    {
        return array_filter(
            $data['Dates'] ?? [],
            fn ($d) => !empty($d['EventId']) && $d['DefaultEventGroupId'] === $data['EventGroupId']
        );
    }

    public function transform(array $data): array
    {
        $transformations = [
            'transformBase',
            'transformOrganizer',
            'transformImages',
            'transformLocation',
            'transformEventSchedule',
            'transformIsAccessibleForFree',
            'transformOffers'
        ];

        $result = array_map(function ($item) use ($transformations) {
            return array_reduce(
                $transformations,
                function ($event, $method) use ($item) {
                    return $this->$method($event, $item);
                },
                Schema::event()
            )->toArray();
        }, $data);
        return $result;
    }

    public function transformBase($event, $data): Event
    {
        return $event
                ->identifier(
                    $this->formatId((string)$data['EventGroupId'] ?? '')
                )
                ->name($data['SubTitle'] ?? null)
                ->description($data['Description'] ?? null)
                ->startDate(
                    min(array_map(
                        fn ($d) => $d['StartDate'] ?? null,
                        [...$this->getValidDatesFromSource($data), null]
                    )) ?? null
                )
                ->endDate(
                    max(array_map(
                        fn ($d) => $d['EndDate'] ?? null,
                        [...$this->getValidDatesFromSource($data), null]
                    )) ?? null
                );
    }

    public function transformOrganizer($event, $data): Event
    {
        return $data['Organisation'] ?? null ? $event->organizer(
            Schema::organization()->name($data['Organisation'] ?? null)
        ) : $event;
    }

    public function transformImages($event, $data): Event
    {
        return $event->image(
            array_map(
                fn ($path) => Schema::imageObject()
                    ->url($path)
                    ->name($data['SubTitle'] ?? null)
                    ->caption($data['SubTitle'] ?? null)
                    ->description($data['SubTitle'] ?? null),
                array_filter(
                    [($data['HasFeaturedImage'] ?? null) ? $data['FeaturedImagePath'] ?? null : null]
                )
            )
        );
    }
    public function transformLocation($event, $data): Event
    {
        foreach ($this->getValidDatesFromSource($data) as $date) {
            return $event->location(
                Schema::place()
                    ->name($date['Venue'] ?? null)
                    ->description($date['Hall'] ?? null)
            );
        }
        return $event;
    }

    public function transformEventSchedule($event, $data): Event
    {
        return $event->eventSchedule(
            array_values(
                array_map(
                    fn($d) => Schema::schedule()
                        ->startDate($d['StartDate'] ?? null)
                        ->endDate($d['EndDate'] ?? null)
                        ->identifier($this->formatId((string)$data['EventGroupId'] ?? '') . '_' . ($d['EventId'] ?? '')),
                    $this->getValidDatesFromSource($data)
                )
            )
        );
    }

    public function transformIsAccessibleForFree($event, $data): Event
    {
        return $event->isAccessibleForFree(
            array_reduce(
                $this->getValidDatesFromSource($data),
                fn ($carry, $d) => $carry || ($d['IsFreeEvent'] ?? false),
                false
            ) || false
        );
    }

    public function transformOffers($event, $data): Event
    {
        $effectivePrices   = $this->firstNonEmptyArray($data['Prices'] ?? null, $data['Dates'][0]['Prices'] ?? null);
        $effectiveProducts = array_values(
            array_filter(
                $this->firstNonEmptyArray(
                    $data['PurchaseUrls'] ?? null,
                    $data['Dates'][0]['PurchaseUrls'] ?? null
                ),
                fn ($d) => $d['Culture'] === 'sv-SE'
            )
        );

        return $event->offers(
            array_values(
                array_map(
                    fn($d) => Schema::offer()
                        ->url($d['Link'] ?? null)
                        ->mainEntityOfPage($d['Link'] ?? null)
                        ->availabilityStarts($data['OnlineSaleStart'] ?? null)
                        ->availabilityEnds($data['OnlineSaleEnd'] ?? null)
                        ->businessFunction('http://purl.org/goodrelations/v1#Sell')
                        ->priceSpecification(
                            array_map(
                                fn ($p) => Schema::priceSpecification()
                                    ->name($p['TicketType'] ?? null)
                                    ->description($p['TicketType'] ?? null)
                                    ->price(
                                        array_filter(
                                            array_values(
                                                array_map(
                                                    fn ($pp) => $pp['Price'] ?? null,
                                                    $p['Prices'] ?? []
                                                )
                                            ),
                                            fn ($val) => is_numeric($val)
                                        )
                                    ),
                                $effectivePrices
                            )
                        ),
                    $effectiveProducts
                )
            )
        );
    }
}
