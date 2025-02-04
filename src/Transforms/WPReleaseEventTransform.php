<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms;

use SchemaTransformer\Interfaces\AbstractDataTransform;
use Spatie\SchemaOrg\Contracts\ImageObjectContract;
use Spatie\SchemaOrg\Event;
use Spatie\SchemaOrg\Schema;

class WPReleaseEventTransform extends TransformBase implements AbstractDataTransform
{
    public function __construct(string $idprefix)
    {
        parent::__construct($idprefix);
    }

    public function transform(array $data): array
    {
        $events = array_map(fn($row) => $this->getEventFromRow($row), $data);
        $events = array_filter($events);

        return array_map(fn($event) => $event->toArray(), $events);
    }

    private function getEventFromRow(array $row): ?Event
    {
        $event = Schema::event();

        if (!$this->rowIsValid($row)) {
            return null;
        }

        $event->identifier($this->formatId($row['id']));
        $event->name($row['title']['rendered']);
        $event->image($this->getImageFromRow($row));

        return $event;
    }

    /**
     * Check if the row is valid and can be transformed
     *
     * @param array $row
     */
    private function rowIsValid(array $row): bool
    {
        if (empty($row['id'])) {
            return false;
        }

        if (empty($row['title']['rendered'])) {
            return false;
        }

        return true;
    }

    private function getImageFromRow(array $row): ?ImageObjectContract
    {
        if (empty($row['_embedded']['wp:featuredmedia'][0]['source_url'])) {
            return null;
        }

        $image = Schema::imageObject();
        $image->url($row['_embedded']['wp:featuredmedia'][0]['source_url']);
        $image->description($row['_embedded']['wp:featuredmedia'][0]['alt_text']);

        return $image;
    }
}
