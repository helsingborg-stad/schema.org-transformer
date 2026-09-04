<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms;

use SchemaTransformer\Interfaces\AbstractDataTransform;
use Municipio\Schema\DayOfWeek;
use Municipio\Schema\Place;
use Municipio\Schema\Schema;

class WPExhibitionEventTransform implements AbstractDataTransform
{
    /**
     * WPExhibitionEventTransform constructor.
     */
    public function __construct()
    {
    }

    public function preprocessData(array $data): array
    {
        return $data;
    }

    public function transform(array $data): array
    {
        return array_map(function ($item) {

            $organizer = Schema::organization()->name($item['acf']['organizer'] ?? null);
            $startDate = $item['acf']['startDate'] ?? null;
            $endDate   = $item['acf']['endDate'] ?? null;
            $eventStatus = $this->getEventStatus($startDate, $endDate);

            $event =
                Schema::exhibitionEvent()
                    ->identifier((string)$item['id'])
                    ->name($item['title']['rendered'] ?? null)
                    ->description($item['acf']['description'] ?? null)
                    ->organizer($organizer)
                    ->startDate($startDate ? \DateTime::createFromFormat('Ymd', $startDate)?->format('Y-m-d') : null)
                    ->endDate($endDate ? \DateTime::createFromFormat('Ymd', $endDate)?->format('Y-m-d') : null)
                    ->location($this->getLocation($item))
                    ->offers($this->getOffers($item))
                    ->image($this->getImages($item));

            if ($eventStatus !== '') {
                $event->keywords([
                    Schema::definedTerm()
                        ->name($eventStatus)
                        ->inDefinedTermSet(Schema::definedTermSet()->name('event_status'))
                ]);
            }

            return $event->toArray();
        }, $data);
    }

    private function getEventStatus(mixed $startDate = null, mixed $endDate = null): string
    {
        $startTimestamp = is_string($startDate) ? strtotime($startDate) : false;

        if ($startTimestamp === false) {
            return '';
        }

        $endTimestamp = is_string($endDate) ? strtotime($endDate) : false;
        $now          = time();

        if ($endTimestamp !== false && $endTimestamp < $now) {
            return 'Avslutad';
        }

        if ($startTimestamp > $now) {
            return 'Kommande';
        }

        return 'Aktuell';
    }

    private function getImages(array $dataItem): array
    {
        return array_map(function ($image) {
            return Schema::imageObject()
                ->url($image['source_url'] ?? null)
                ->name($image['title']['rendered'] ?? null)
                ->description($image['alt_text'] ?? null)
                ->toArray();
        }, $dataItem['_embedded']['acf:attachment'] ?? []);
    }

    private function getOffers($dataItem): array
    {
        return array_map(function ($offer) {
            $price = $offer['amount'] ?: 0;
            return [
                'name'          => $offer['name'] ?? null,
                'price'         => $offer['free'] === true ? 0 : $price,
                'priceCurrency' => 'SEK',
            ];
        }, $dataItem['acf']['offers'] ?? []);
    }

    private function getLocation($dataItem): Place
    {
        return Schema::place()
            ->name($dataItem['acf']['locationname'] ?? null)
            ->address($dataItem['acf']['location']['address'] ?? null)
            ->latitude($dataItem['acf']['location']['lat'] ?? null)
            ->longitude($dataItem['acf']['location']['lng'] ?? null)
            ->openingHoursSpecification($this->getOpeningHoursSpecifications($dataItem));
    }

    private function getOpeningHoursSpecifications(array $dataItem): array
    {
        return array_map(function ($item) {
            return Schema::openingHoursSpecification()
                ->dayOfWeek($this->mapDayOfWeek($item['days']))
                ->opens($item['opens'] ?? null)
                ->closes($item['closes'] ?? null);
        }, $dataItem['acf']['openingHoursSpecification'] ?? []);
    }

    private function mapDayOfWeek(array $dayOfWeekArray): array
    {
        return array_map(function ($day) {
            return match ($day) {
                'monday' => DayOfWeek::Monday,
                'tuesday' => DayOfWeek::Tuesday,
                'wednesday' => DayOfWeek::Wednesday,
                'thursday' => DayOfWeek::Thursday,
                'friday' => DayOfWeek::Friday,
                'saturday' => DayOfWeek::Saturday,
                'sunday' => DayOfWeek::Sunday,
                default => null,
            };
        }, $dayOfWeekArray);
    }
}
