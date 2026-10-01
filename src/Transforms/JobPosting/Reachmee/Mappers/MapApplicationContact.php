<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers;

use Municipio\Schema\JobPosting;
use Municipio\Schema\Schema;

class MapApplicationContact extends AbstractReachmeeJobPostingMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(JobPosting $jobPosting, array $data): JobPosting
    {
        return $jobPosting->applicationContact(
            array_values(
                array_map(
                    fn($contact) => Schema::contactPoint()
                        ->contactType($contact['position'])
                        ->name($contact['first_name'] . ' ' . $contact['surname'])
                        ->email($contact['email'])
                        ->telephone($contact['phone']),
                    $data['contact_persons'] ?? []
                )
            )
        );
    }
}
