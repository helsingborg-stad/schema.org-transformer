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
            Schema::textObject()->text($data['description'] ?? '')->name('Beskrivning'),
            $this->makeBulletSection(
                'Mål',
                array_filter(array_values(array_map(
                    fn($goal) => $goal['name'] ?? null,
                    $data['goals'] ?? []
                )))
            ),

            $this->makeBulletSection(
                'Risker',
                array_filter(array_values(array_map(
                    fn($risk) => $risk['description'] ?? null,
                    $data['risks'] ?? []
                )))
            )
        ];

        return $project->description(array_values(array_filter($sections)));
    }

    private function makeBulletSection(string $name, array $items): TextObject|null
    {
        if (empty($items)) {
            return null;
        }
        // $result = "<h2>{$name}</h2><ul>";
        $result = "<ul>";
        foreach ($items as $item) {
            $result .= "<li>{$item}</li>";
        }
        $result .= '</ul>';
        return Schema::textObject()->text($result)->headline($name)->name($name);
    }
}
