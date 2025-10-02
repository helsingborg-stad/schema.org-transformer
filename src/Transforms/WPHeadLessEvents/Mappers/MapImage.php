<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPHeadLessEvents\Mappers;

use Municipio\Schema\Event;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\AbstractWPHeadlessEventMapper;

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
                        fn ($fm) => $fm['media_type'] === 'image' && !empty($fm['media_details']['sizes']['full'])
                            ? Schema::imageObject()
                                ->url($fm['media_details']['sizes']['full']['source_url'])
                                ->name($fm['title']['rendered'] ?? null)
                                ->description($fm['alt_text'] ?? null)
                                ->caption($fm['title']['rendered'] ?? null)
                            : null,
                        $data['_embedded']['wp:featuredmedia'] ?? [],
                    )
                )
            )
        );
    }
}
