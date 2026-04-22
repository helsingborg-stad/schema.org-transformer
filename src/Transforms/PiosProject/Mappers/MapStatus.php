<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\PiosProject\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\Project;

class MapStatus extends AbstractPiosProjectMapper
{
    private array $phases = [
        'NotSet'                             => 'Ingen status',
        'Draft'                              => 'Utkast',
        'Ready'                              => 'Redo',
        'ReadyForReview'                     => 'Redo för granskning',
        'Published'                          => 'Publicerad',
        'NotStartedDecided'                  => 'Ej startad',
        'Started'                            => 'Startad',
        'Finished'                           => 'Avslutad',
        'Paused'                             => 'Pausad',
        'FinishedEffectRealizationStarted'   => 'Inväntar effekthemtagning',
        'FinishedEffectRealizationConcluded' => 'Effekthemtagning avslutad'
    ];

    private array $statuses = [
        'Zero'  => 0,
        'One'   => 33,
        'Two'   => 66,
        'Three' => 100
    ];

    public function map(Project $project, array $data): Project
    {
        return $project->status(
            Schema::progressStatus()
                ->minNumber(0)
                ->maxNumber(100)
                ->name($this->phases[$data['projectPhase'] ?? 'NotSet'] ?? null)
                ->number($this->statuses[$data['projectStatus'] ?? 'Zero'] ?? 0)
        );
    }
}
