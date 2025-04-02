<?php

namespace SchemaTransformer\Transforms\WPLegacyEventTransform;

use SchemaTransformer\Interfaces\SchemaFactory;
use Municipio\Schema\BaseType;
use Municipio\Schema\Event;
use Municipio\Schema\Schema;

class EventFactory implements SchemaFactory
{
    public function createSchema(array $data): BaseType
    {
        return new class extends Event {
            public function toArray(): array
            {
                $array = parent::toArray();

                $array['@type']    = "schema:Event";
                $array['@context'] = [
                    'schema'    => 'https://schema.org',
                    'municipio' => 'https://schema.municipio.tech/schema.jsonld',
                ];

                return $array;
            }
        };
    }
}
