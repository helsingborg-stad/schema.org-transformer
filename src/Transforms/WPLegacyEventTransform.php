<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms;

use SchemaTransformer\Interfaces\AbstractDataTransform;
use Spatie\SchemaOrg\Schema;

class WPLegacyEventTransform extends TransformBase implements AbstractDataTransform
{
    public function __construct(string $idprefix)
    {
        parent::__construct($idprefix);
    }

    public function transformStatus(string $data): string
    {
        switch ($data) {
            case "scheduled":
                return "EventScheduled";
            case "rescheduled":
                return "EventRescheduled";
            case "cancelled":
                return "EventCancelled";
        }
        return "EventScheduled";
    }

    public function transform(array $data): array
    {
        $output = [];
        foreach ($data as &$row) {
            $event = Schema::event()
                ->name($row["title"]["rendered"] ?? "");
            $event->identifier($this->formatId($row['id'] ?? ""));
            $location = Schema::place()
                ->address(Schema::postalAddress()
                    ->streetAddress($row["location"]["street_address"] ?? "")
                    ->postalCode($row["location"]["postal_code"] ?? "")
                    ->addressLocality($row["location"]["city"] ?? "")
                    ->addressRegion($row["location"]["municipality"] ?? "")
                    ->addressCountry("SE"))
                ->latitude($row["location"]["latitude"] ?? "")
                ->longitude($row["location"]["longitude"] ?? "");
            $event->location($location);

            if ($this->isValidArray($row, 'gallery')) {
                $images = [];
                foreach ($row["gallery"] as &$image) {
                    $images[] = Schema::imageObject()
                        ->contentUrl($image["url"] ?? "")
                        ->contentLocation($image["description"] ?? "")
                        ->contentSize($image["filesize"] ?? "")
                        ->datePublished($image["date"] ?? "")
                        ->width($image["width"] ?? "")
                        ->height($image["height"] ?? "")
                        ->caption($image["caption"] ?? "")
                        ->thumbnail(Schema::imageObject()
                            ->contentUrl($image["sizes"]["thumbnail"] ?? "")
                            ->width($image["sizes"]["thumbnail-width"] ?? "")
                            ->height($image["sizes"]["thumbnail-height"] ?? ""));
                }
                $event->image($images);
            }
            if ($this->isValidArray($row, 'occasions')) {
                $occasions = [];
                foreach ($row["occasions"] as &$occasion) {
                    $occasions[] = Schema::Event()
                        ->startDate($occasion["start_date"] ?? "")
                        ->endDate($occasion["end_date"] ?? "")
                        ->doorTime($occasion["door_time"] ?? "")
                        ->eventStatus($this->transformStatus($occasion["status"] ?? ""));
                }
                $event->subEvents($occasions);
            }
            $event->setProperty('@version', md5(json_encode($event->toArray())));
            $output[] = $event->toArray();
        }
        return $output;
    }
}
