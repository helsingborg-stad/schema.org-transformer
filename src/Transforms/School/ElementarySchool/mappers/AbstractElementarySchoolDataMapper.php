<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\ElementarySchool\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\TextObject;
use Municipio\Schema\ElementarySchool;
use SchemaTransformer\Transforms\School\ElementarySchool\Mappers\ElementarySchoolDataMapperInterface;
use SchemaTransformer\Transforms\TransformBase;

abstract class AbstractElementarySchoolDataMapper implements ElementarySchoolDataMapperInterface
{
    private array $wellknownTextObjectHeadlinesByKey = [
        'custom_excerpt'     => '',
        'about_us'           => 'Om oss',
        'how_we_work'        => 'Så arbetar vi',
        'our_leisure_center' => 'Vårt fritidshem'
    ];

    public function __construct(private ?TransformBase $transform = null)
    {
    }

    abstract public function map(ElementarySchool $school, array $data): ElementarySchool;

    protected function formatId(string | int $value): string
    {
        return $this->transform->formatId($value);
    }

    protected function tryCreateTextObject($key, $text): ?TextObject
    {
        if (is_string($key) && is_string($text) && !(empty($key) || empty($text))) {
            return Schema::textObject()
                ->name($key)
                ->headline($this->wellknownTextObjectHeadlinesByKey[$key] ?? $key)
                ->text($text);
        }
        return null;
    }

    protected function tryMapPositiveInt($value): ?int
    {
        return is_numeric($value) && (int)($value) > 0 ? (int)($value) : null;
    }
}
