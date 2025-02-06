<?php

declare(strict_types=1);

namespace SchemaTransformer\Services;

use SchemaTransformer\Interfaces\AbstractDataConverter;
use SchemaTransformer\Interfaces\AbstractDataReader;
use SchemaTransformer\Interfaces\AbstractDataWriter;
use SchemaTransformer\Services\Service;
use SchemaTransformer\Interfaces\AbstractService;
use SchemaTransformer\Transforms\DataSanitizers\SanitizeReachmeeJobPostingLink;
use SchemaTransformer\Transforms\ReachmeeJobPostingTransform;
use SchemaTransformer\Transforms\SplitRowsByOccasion;
use SchemaTransformer\Transforms\StratsysTransform;
use SchemaTransformer\Transforms\WPLegacyEventTransform;
use SchemaTransformer\Transforms\WPReleaseEventTransform;

class RuntimeServices
{
    private AbstractService $jobPostingService;
    private AbstractService $stratsysService;
    private AbstractService $wpLegacyEventService;
    private AbstractService $wpReleaseEventService;

    public function __construct(
        AbstractDataReader $reader,
        AbstractDataWriter $writer,
        AbstractDataConverter $converter,
        string $idprefix
    ) {
        $reachmeeJobPostingSanitizers = [
            new SanitizeReachmeeJobPostingLink()
        ];

        $this->jobPostingService     = new Service(
            $reader,
            $writer,
            new ReachmeeJobPostingTransform($reachmeeJobPostingSanitizers, $idprefix),
            $converter
        );
        $this->stratsysService       = new Service(
            $reader,
            $writer,
            new StratsysTransform($idprefix),
            $converter
        );
        $this->wpLegacyEventService  = new Service(
            $reader,
            $writer,
            new WPLegacyEventTransform($idprefix),
            $converter
        );
        $this->wpReleaseEventService = new Service(
            $reader,
            $writer,
            new WPReleaseEventTransform(
                $idprefix,
                new \SchemaTransformer\Transforms\SplitRowsByOccasion(),
                new \SchemaTransformer\Transforms\WPReleaseEventTransform\EventFactory(),
                [
                    new \SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators\ApplyAudience(),
                    new \SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators\ApplyDescription(),
                    new \SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators\ApplyEndDate(),
                    new \SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators\ApplyEventStatus(),
                    new \SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators\ApplyImage(),
                    new \SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators\ApplyIsAccessibleForFree(),
                    new \SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators\ApplyLocation(),
                    new \SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators\ApplyMeta(),
                    new \SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators\ApplyName(),
                    new \SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators\ApplyOffers(),
                    new \SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators\ApplyStartDate(),
                    new \SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators\ApplyTypicalAgeRange(),
                    new \SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators\ApplyOrganizer(),
                    new \SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators\ApplyEventAttendanceMode(),
                ],
                new \SchemaTransformer\Transforms\WPReleaseEventTransform\EventValidator()
            ),
            $converter
        );
    }
    public function getJobPostingService(): AbstractService
    {
        return $this->jobPostingService;
    }
    public function getStratsysService(): AbstractService
    {
        return $this->stratsysService;
    }
    public function getWPLegacyEventService(): AbstractService
    {
        return $this->wpLegacyEventService;
    }
    public function getWPReleaseEventService(): AbstractService
    {
        return $this->wpReleaseEventService;
    }
}
