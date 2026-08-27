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
        'role:preamble' => '',
        'visit_us'      => 'Besök oss',
        'about_us'      => 'Om oss',
        'how_we_work'   => 'Så arbetar vi',
        'orientation'   => 'Introduktion',
    ];

    public function __construct(private ?TransformBase $transform = null)
    {
    }

    abstract public function map(Preschool $school, array $data): Preschool;

    protected function formatId(string | int $value): string
    {
        return $this->transform->formatId($value);
    }

    public function tryCreateTextObject($key, $text, $headline = null): ?TextObject
    {
        if (is_string($key) && is_string($text) && !(empty($key) || empty($text))) {
            return Schema::textObject()
                ->name($key)
                ->headline($this->wellknownTextObjectHeadlinesByKey[$key] ?? $headline ?? $key)
                ->text($text);
        }
        return null;
    }

    public function tryCreateTextObjectWithHeadline($key, $text, $headline): ?TextObject
    {
        return is_string($headline) && !empty($headline) ? $this->tryCreateTextObject($key, $text, $headline) : null;
    }

    protected function tryMapPositiveInt($value): ?int
    {
        return is_numeric($value) && (int)($value) > 0 ? (int)($value) : null;
    }
}
