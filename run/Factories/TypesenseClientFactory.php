<?php

namespace SchemaTransformer\Run\Factories;

class TypesenseClientFactory
{
    public static function create(): \Typesense\Client
    {
        return new \Typesense\Client([
            'api_key' => getenv('TYPESENSE_API_KEY'),
            'nodes'   => [
                [
                    'host'     => getenv('TYPESENSE_HOST'),
                    'port'     => (int)getenv('TYPESENSE_PORT'),
                    'protocol' => getenv('TYPESENSE_PROTOCOL'),
                ],
            ],
        ]);
    }
}
