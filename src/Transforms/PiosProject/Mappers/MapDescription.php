<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\PiosProject\Mappers;

use Municipio\Schema\Project;
use Municipio\Schema\TextObject;
use Municipio\Schema\Schema;

class MapDescription extends AbstractPiosProjectMapper
{
    public function map(Project $project, array $data): Project
    {
        $sections = [
            $this->makeSection($data['description'] ?? null, 'description'),
            $this->makeSection($data['benefitsAndEffects'] ?? null, 'benefitsAndEffects', '<h2>Nyttor och effekter</h2>'),
            $this->makeBulletSection(
                array_filter(array_values(array_map(
                    fn($goal) => $goal['name'] ?? null,
                    $data['goals'] ?? []
                ))),
                'goals',
                '<h2>Mål</h2>'
            ),

            ...array_filter(
                array_map(
                    fn($dim) => $this->makeBulletSection(
                        $dim['values'] ?? [],
                        null,
                        "<h2>{$dim['name']}</h2>",
                        $dim['value'] ?? null
                    ),
                    $data['customDimensions'] ?? []
                )
            ),
/*
            $this->makeBulletSection(
                array_filter(array_values(array_map(
                    fn($risk) => $risk['description'] ?? null,
                    $data['risks'] ?? []
                ))),
                'risks',
                '<h2>Risker</h2>'
            )
*/
        ];

        return $project->description(array_values(array_filter($sections)));
    }

    private function makeSection(?string $text, string $name, ?string $headline = null): TextObject|null
    {
        return empty($text) ? null : Schema::textObject()->text($text)->headline($headline)->name($name);
    }

    private function makeBulletSection(array $items, ?string $name = null, ?string $headline = null, ?string $preface = null): TextObject|null
    {
        if (empty($items) && empty($preface)) {
            return null;
        }

        $result = $preface ?? '';
        if (!empty($items)) {
            $result .= "<ul>";
            foreach ($items as $item) {
                $result .= "<li>{$item}</li>";
            }
            $result .= '</ul>';
        }
        return Schema::textObject()->text($result)->headline($headline)->name($name);
    }
}
