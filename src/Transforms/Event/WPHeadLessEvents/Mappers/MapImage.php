<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\WPHeadLessEvents\Mappers;

use Municipio\Schema\Event;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\Event\WPHeadLessEvents\Mappers\AbstractWPHeadlessEventMapper;

class MapImage extends AbstractWPHeadlessEventMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Event $event, array $data): Event
    {
        return $event->image(
            array_values(
                array_filter(
                    array_map(
                        fn ($a) => $this->getImageUrlFromAttachment($a)
                            ? Schema::imageObject()
                                ->url($this->getImageUrlFromAttachment($a))
                                ->name($a['title']['rendered'] ?? null)
                                ->description($a['alt_text'] ?? null)
                                ->caption($a['title']['rendered'] ?? null)
                            : null,
                        $data['_embedded']['acf:attachment'] ?? [],
                    )
                )
            )
        );
    }

    private function getImageUrlFromAttachment(array $a): ?string
    {
        return (($a['media_type'] ?? null) === 'image')
            ? $a['media_details']['sizes']['full']['source_url'] ?? $a['source_url'] ?? null
            : null;
    }
}
