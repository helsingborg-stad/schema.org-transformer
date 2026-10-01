<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\Reachmee;

use SchemaTransformer\Interfaces\AbstractDataTransform;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\TransformBase;
use SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\MapApplicationContact;
use SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\MapDatePosted;
use SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\MapDescription;
use SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\MapDirectApply;
use SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\MapEmployerOverview;
use SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\MapEmploymentType;
use SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\MapEmploymentUnit;
use SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\MapHiringOrganization;
use SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\MapIdentifier;
use SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\MapImage;
use SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\MapRelevantOccupation;
use SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\MapTitle;
use SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\MapUrl;
use SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\MapValidThrough;
use SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\MapWorkHours;
use SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\MapXCreatedBy;

class ReachmeeJobPostingTransform extends TransformBase implements AbstractDataTransform
{
    public function __construct(string $idprefix = '')
    {
        parent::__construct($idprefix);
    }

    public function transform(array $data): array
    {
        $mappers = [
            new MapApplicationContact(),
            new MapDatePosted(),
            new MapDescription(),
            new MapDirectApply(),
            new MapEmployerOverview(),
            new MapEmploymentType(),
            new MapEmploymentUnit(),
            new MapHiringOrganization(),
            new MapIdentifier($this),
            new MapImage(),
            new MapRelevantOccupation(),
            new MapTitle(),
            new MapUrl(),
            new MapValidThrough(),
            new MapWorkHours(),
            new MapXCreatedBy()
        ];

        $result = array_map(function ($item) use ($mappers) {
            return array_reduce(
                $mappers,
                function ($event, $mapper) use ($item) {
                    return $mapper->map($event, $item);
                },
                Schema::jobPosting()
            )->toArray();
        }, array_values(array_filter($data, fn($item) => isset($item['project_id']))));
        return $result;
    }
}
