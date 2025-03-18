<?php

namespace SchemaTransformer\Transforms\WPLegacyEventTransform;

use SchemaTransformer\Interfaces\SchemaFactory;
use Spatie\SchemaOrg\BaseType;
use Spatie\SchemaOrg\Event;
use Spatie\SchemaOrg\Schema;

class EventFactory implements SchemaFactory
{
    public function createSchema(array $data): BaseType
    {
        return new class extends Event {
            public function toArray(): array
            {
                $this->serializeIdentifier();
                $properties = $this->serializeProperty($this->getProperties());

                return [
                    '@context' => [
                        'schema'    => 'https://schema.org',
                        'municipio' => 'https://schema.municipio.tech/schema.jsonld',
                    ],
                    '@type'    => $this->getType(),
                ] + $properties;
            }
        };
    }
}
