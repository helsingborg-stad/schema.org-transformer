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
    private function getLocation(array | null $data)
    {
        return empty($data) ? null :
            Schema::place()
            ->address(Schema::postalAddress()
                ->alternateName($data["formatted_address"] ?? null)
                ->streetAddress($data["street_address"] ?? null)
                ->postalCode($data["postal_code"] ?? null)
                ->addressLocality($data["city"] ?? null)
                ->addressRegion($data["municipality"] ?? null)
                ->addressCountry("SE"))
            ->latitude($data["latitude"] ?? null)
            ->longitude($data["longitude"] ?? null);
    }
    private function getContactPoint(array | null $data)
    {
        return empty($data) ? null :
            Schema::contactPoint()
            ->name($data["person_name"] ?? null)
            ->email($data["person_email"] ?? null)
            ->telephone($data["person_phone"] ?? null);
    }
    private function getOrganizer(array | null $data)
    {
        return empty($data) ? null :
            Schema::organization()
            ->name($data["organizer"] ?? null)
            ->url($data["organizer_link"] ?? null)
            ->telephone($data["organizer_phone"] ?? null)
            ->email($data["organizer_email"] ?? null)
            ->contactPoint(
                array_map(function ($row) {
                    return $this->getContactPoint($row);
                }, is_array($data["contact_persons"]) ? $data["contact_persons"] : [])
            );
    }
    public function transform(array $data): array
    {
        $output = [];
        foreach ($data as &$row) {
            $event = Schema::event()
                ->identifier($this->formatId($row['id'] ?? null))
                ->name($row["title"]["rendered"] ?? null)
                ->description($row["content"]["rendered"] ?? null)
                ->url($row["event_link" ?? null]);

            $event->location([
                $this->getLocation($row["location"]),
                ...array_map(function ($row) {
                    return $this->getLocation($row);
                }, $row["additional_locations"] ?? [])
            ]);

            $event->organizer(
                array_map(function ($row) {
                    return $this->getOrganizer($row);
                }, $row["organizers"] ?? [])
            );

            if ($this->isValidArray($row, 'gallery')) {
                $images = [];
                foreach ($row["gallery"] as &$image) {
                    $images[] = Schema::imageObject()
                        ->contentUrl($image["url"] ?? null)
                        ->contentLocation($image["description"] ?? null)
                        ->contentSize($image["filesize"] ?? null)
                        ->datePublished($image["date"] ?? null)
                        ->width($image["width"] ?? null)
                        ->height($image["height"] ?? null)
                        ->caption($image["caption"] ?? null)
                        ->thumbnail(Schema::imageObject()
                            ->contentUrl($image["sizes"]["thumbnail"] ?? null)
                            ->width($image["sizes"]["thumbnail-width"] ?? null)
                            ->height($image["sizes"]["thumbnail-height"] ?? null));
                }
                $event->image($images);
            }
            if ($this->isValidArray($row, 'occasions')) {
                $occasions = [];
                foreach ($row["occasions"] as &$occasion) {
                    $occasions[] = Schema::Event()
                        ->startDate($occasion["start_date"] ?? null)
                        ->endDate($occasion["end_date"] ?? null)
                        ->doorTime($occasion["door_time"] ?? null)
                        ->eventStatus($this->transformStatus($occasion["status"] ?? ""));
                }
                $event->subEvents($occasions);
            }

            $event->keywords($row["event_tags"] ?? null);

            $event->offers(array_filter([
                Schema::offer()
                    ->price($row["price_adult"] ?? null)
                    ->category("ADULT"),
                Schema::offer()
                    ->price($row["price_children"] ?? null)
                    ->category("CHILDREN"),
                Schema::offer()
                    ->price($row["price_student"] ?? null)
                    ->category("STUDENT"),
                Schema::offer()
                    ->price($row["price_senior"] ?? null)
                    ->category("SENIOR")
            ], function ($offer) {
                return !empty($offer["price"]);
            }));
            $event->subjectOf(Schema::creativeWork()
                ->dateCreated($row["date"] ?? null)
                ->dateModified($row["modified"] ?? null)
                ->keywords($row["event_categories"] ?? null));
            /*
            $event->setProperty('@meta', [
                Schema::propertyValue()->name('status')->value($row["status"] ?? null),
                Schema::propertyValue()->name('featured_media')->value($row["featured_media"] ?? null),
                Schema::propertyValue()->name('user_groups')->value($row["user_groups"] ?? null),
                Schema::propertyValue()->name('event_categories')->value($row["event_categories"] ?? null),
                Schema::propertyValue()->name('additional_links')->value($row["additional_links"] ?? null),
                Schema::propertyValue()->name('related_events')->value($row["related_events"] ?? null),
                Schema::propertyValue()->name('supporters')->value($row["supporters"] ?? null),
                Schema::propertyValue()->name('booking_link')->value($row["booking_link"] ?? null),
                Schema::propertyValue()->name('booking_link_type')->value($row["booking_link_type"] ?? null),
                Schema::propertyValue()->name('booking_email')->value($row["booking_email"] ?? null),
                Schema::propertyValue()->name('ticket_release_date')->value($row["ticket_release_date"] ?? null),
                Schema::propertyValue()->name('age_restriction')->value($row["age_restriction"] ?? null),
                Schema::propertyValue()->name('ticket_stock')->value($row["ticket_stock"] ?? null),
                Schema::propertyValue()->name('tickets_remaining')->value($row["tickets_remaining"] ?? null),
                Schema::propertyValue()->name('additional_ticket_retailers')->value($row["additional_ticket_retailers"] ?? null),
                Schema::propertyValue()->name('membership_cards')->value($row["membership_cards"] ?? null),
                Schema::propertyValue()->name('price_information')->value($row["price_information"] ?? null),
                Schema::propertyValue()->name('ticket_includes')->value($row["ticket_includes"] ?? null),
                Schema::propertyValue()->name('price_adult')->value($row["price_adult"] ?? null),
                Schema::propertyValue()->name('price_children')->value($row["price_children"] ?? null),
                Schema::propertyValue()->name('children_age')->value($row["children_age"] ?? null),
                Schema::propertyValue()->name('price_student')->value($row["price_student"] ?? null),
                Schema::propertyValue()->name('price_senior')->value($row["price_senior"] ?? null),
                Schema::propertyValue()->name('additional_ticket_types')->value($row["additional_ticket_types"] ?? null),
                Schema::propertyValue()->name('senior_age')->value($row["senior_age"] ?? null),
                Schema::propertyValue()->name('booking_group')->value($row["booking_group"] ?? null),
                Schema::propertyValue()->name('price_range')->value($row["price_range"] ?? null),
                Schema::propertyValue()->name('contact_phone')->value($row["contact_phone"] ?? null),
                Schema::propertyValue()->name('contact_email')->value($row["contact_email"] ?? null),
                Schema::propertyValue()->name('contact_information')->value($row["contact_information"] ?? null),
                Schema::propertyValue()->name('gallery')->value($row["gallery"] ?? null),
                Schema::propertyValue()->name('facebook')->value($row["facebook"] ?? null),
                Schema::propertyValue()->name('twitter')->value($row["twitter"] ?? null),
                Schema::propertyValue()->name('instagram')->value($row["instagram"] ?? null),
                Schema::propertyValue()->name('google_music')->value($row["google_music"] ?? null),
                Schema::propertyValue()->name('apple_music')->value($row["apple_music"] ?? null),
                Schema::propertyValue()->name('spotify')->value($row["spotify"] ?? null),
                Schema::propertyValue()->name('soundcloud')->value($row["soundcloud"] ?? null),
                Schema::propertyValue()->name('deezer')->value($row["deezer"] ?? null),
                Schema::propertyValue()->name('youtube')->value($row["youtube"] ?? null),
                Schema::propertyValue()->name('vimeo')->value($row["vimeo"] ?? null),
                Schema::propertyValue()->name('age_group_from')->value($row["age_group_from"] ?? null),
                Schema::propertyValue()->name('age_group_to')->value($row["age_group_to"] ?? null),
                Schema::propertyValue()->name('accessibility')->value($row["accessibility"] ?? null),
                Schema::propertyValue()->name('lang')->value($row["lang"] ?? null),
                Schema::propertyValue()->name('translations')->value($row["translations"] ?? null)
            ]);
*/
            $event->setProperty('@version', md5(json_encode($event->toArray())));
            $output[] = $event->toArray();
        }
        return $output;
    }
}
