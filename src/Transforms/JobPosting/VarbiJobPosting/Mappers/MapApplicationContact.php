<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers;

use Municipio\Schema\JobPosting;
use Municipio\Schema\Schema;

class MapApplicationContact extends AbstractVarbiJobPostingMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(JobPosting $jobPosting, array $data): JobPosting
    {
        return $jobPosting->applicationContact(
            array_values(array_filter(
                array_map(
                    fn($contact) => Schema::contactPoint()
                            ->contactType($contact['title'] ?? null)
                            ->name($contact['name'] ?? null)
                            ->email($contact['email'])
                            ->telephone($contact['phone']),
                    $this->getIncludedAd($data)['attributes']['contacts'] ?? []
                )
            ))
        );
    }
}
