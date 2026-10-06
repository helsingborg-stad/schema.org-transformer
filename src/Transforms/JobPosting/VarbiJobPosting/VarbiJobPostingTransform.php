<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\VarbiJobPosting;

use SchemaTransformer\Interfaces\AbstractDataTransform;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\TransformBase;
use SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\MapApplicationContact;
use SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\MapDatePosted;
use SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\MapDescription;
use SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\MapDirectApply;
use SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\MapEmployerOverview;
use SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\MapEmploymentType;
use SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\MapEmploymentUnit;
use SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\MapHiringOrganization;
use SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\MapIdentifier;
use SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\MapImage;
use SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\MapRelevantOccupation;
use SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\MapTitle;
use SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\MapUrl;
use SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\MapValidThrough;
use SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\MapWorkHours;
use SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\MapXCreatedBy;

class VarbiJobPostingTransform extends TransformBase implements AbstractDataTransform
{
    public function __construct(string $idprefix)
    {
        parent::__construct($idprefix);
    }

    public function preprocessData(array $data): array
    {
        return $data;
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
        }, array_values($data));
        return $result;
    }
}
