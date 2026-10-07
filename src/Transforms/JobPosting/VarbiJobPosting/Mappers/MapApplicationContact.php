<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers;

use Municipio\Schema\JobPosting;
use Municipio\Schema\Schema;

class MapApplicationContact extends AbstractVarbiJobPostingMapper
{
    public const string DEFAULT_CONTACT_TYPE       = 'Kontakt';
    public const string DEFAULT_UNION_CONTACT_TYPE = 'Facklig företrädare';

    public function __construct()
    {
        parent::__construct();
    }

    public function map(JobPosting $jobPosting, array $data): JobPosting
    {
        return $jobPosting->applicationContact(
            array_values(array_filter(
                [
                    ...array_map(
                        fn($contact) => Schema::contactPoint()
                                ->contactType($contact['title'] ?? self::DEFAULT_CONTACT_TYPE)
                                ->name($contact['name'] ?? null)
                                ->email($contact['email'] ?? null)
                                ->telephone($contact['phone'] ?? null),
                        $this->getIncludedAd($data)['attributes']['contacts'] ?? []
                    ),
                    ...array_map(
                        fn($contact) => Schema::contactPoint()
                                ->contactType($contact['title'] ?? self::DEFAULT_UNION_CONTACT_TYPE)
                                ->name($contact['name'] ?? null)
                                ->email($contact['email'] ?? null)
                                ->telephone($contact['phone'] ?? null),
                        $this->getIncludedAd($data)['attributes']['union_contacts'] ?? []
                    ),
                ]
            ))
        );
    }
}
