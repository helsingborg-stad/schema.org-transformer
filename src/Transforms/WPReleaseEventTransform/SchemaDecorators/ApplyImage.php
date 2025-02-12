<?php

namespace SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators;

use SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorator;
use Spatie\SchemaOrg\BaseType;
use Spatie\SchemaOrg\Schema;

class ApplyImage implements SchemaDecorator
{
    public function apply(BaseType $event, array $data): BaseType
    {
        if (empty($data['_embedded']['wp:featuredmedia'][0]['source_url'])) {
            return $event;
        }

        $url = $data['_embedded']['wp:featuredmedia'][0]['source_url'];
        $alt = $data['_embedded']['wp:featuredmedia'][0]['alt_text'] ?? null;

        if (empty($url)) {
            return $event;
        }

        return $event->setProperty('image', Schema::imageObject()->url($url)->description($alt));
    }
}
