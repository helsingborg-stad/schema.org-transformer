<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPHeadLessEvents\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\Event;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\AbstractWPHeadlessEventMapper;

class MapKeywords extends AbstractWPHeadlessEventMapper
{
    private array $ignoredTaxonomies = [
        'organization'  => true,
        'accessibility' => true
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
                            : $this->tryMapDefinedTerm($term['name'] ?? null, $term['taxonomy'] ?? null),
                        array_merge(...($data['_embedded']['wp:term'] ?? []))
                    )
                )
            )
        );
    }

    private function tryMapDefinedTerm(?string $name, ?string $taxonomy): ?\Municipio\Schema\DefinedTerm
    {
        if (empty($name)) {
            return null;
        }
        if (empty($taxonomy)) {
            return null;
        }
        return Schema::definedTerm()
            ->name($name)
            ->inDefinedTermSet(Schema::definedTermSet()->name($taxonomy));
    }
}
