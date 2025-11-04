<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\PreSchool\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\TextObject;
use Municipio\Schema\Preschool;
use SchemaTransformer\Transforms\School\PreSchool\Mappers\PreSchoolDataMapperInterface;
use SchemaTransformer\Transforms\TransformBase;

abstract class AbstractPreSchoolDataMapper implements PreSchoolDataMapperInterface
{
    private array $wellknownTextObjectHeadlinesByKey = [
        'custom_excerpt' => '',
        'visit_us'       => 'Besök oss',
        'about_us'       => 'Om oss',
        'how_we_work'    => 'Så arbetar vi',
        'orientation'    => 'Introduktion',
    ];

    public function __construct(private ?TransformBase $transform = null)
    {
    }

    abstract public function map(Preschool $school, array $data): Preschool;

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
}
