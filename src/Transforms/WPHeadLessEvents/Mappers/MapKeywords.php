<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPHeadLessEvents\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\Event;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\AbstractWPHeadlessEventMapper;

class MapKeywords extends AbstractWPHeadlessEventMapper
{
    private array $ignoredTaxonomies = [
        'organization' => true
    ];
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Event $event, array $data): Event
    {
        return $event->keywords(
            array_values(
                array_filter(
                    array_map(
                        fn ($term) => $this->ignoredTaxonomies[$term['taxonomy'] ?? ''] ?? false
                            ? null
                            : Schema::definedTerm()
                                ->name($term['name'])
                                ->inDefinedTermSet(Schema::definedTermSet()->name($term['taxonomy'])),
                        $data['_embedded']['acf:term'] ?? []
                    )
                )
            )
        );
    }
}
