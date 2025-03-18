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
        $pathValueAccessor            = new \SchemaTransformer\Util\ArrayPathResolver();
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
            new WPLegacyEventTransform(
                $idprefix,
                new \SchemaTransformer\Transforms\SplitRowsByOccasion('occasions'),
                new \SchemaTransformer\Transforms\WPLegacyEventTransform\EventFactory(),
                [
                    new \SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators\ApplyName('title.rendered', $pathValueAccessor),
                    new \SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators\ApplyDescription('content.rendered', $pathValueAccessor),
                    new \SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators\ApplyLocationPlace('location.title', 'location.formatted_address', 'location.latitude', 'location.longitude', $pathValueAccessor),
                    new \SchemaTransformer\Transforms\WPLegacyEventTransform\SchemaDecorators\ApplyStartDate(),
                    new \SchemaTransformer\Transforms\WPLegacyEventTransform\SchemaDecorators\ApplyEndDate(),
                    new \SchemaTransformer\Transforms\WPLegacyEventTransform\SchemaDecorators\ApplyEventStatus(),
                    new \SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators\ApplyImage(),
                    new \SchemaTransformer\Transforms\WPLegacyEventTransform\SchemaDecorators\ApplyKeywords(),
                    new \SchemaTransformer\Transforms\WPLegacyEventTransform\SchemaDecorators\ApplyEventAttendanceMode(),
                    new \SchemaTransformer\Transforms\WPLegacyEventTransform\SchemaDecorators\ApplyOrganizer(),
                    new \SchemaTransformer\Transforms\WPLegacyEventTransform\SchemaDecorators\ApplyPhysicalAccessibilityFeatures(),
                    new \SchemaTransformer\Transforms\WPLegacyEventTransform\SchemaDecorators\ApplyTypicalAgeRange(),
                    new \SchemaTransformer\Transforms\WPLegacyEventTransform\SchemaDecorators\ApplyOffers(),
                    new \SchemaTransformer\Transforms\WPLegacyEventTransform\SchemaDecorators\ApplyUrl(),
                    new \SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators\ApplyIdAsDefinedTermInKeywords($idprefix),
                ],
                new \SchemaTransformer\Transforms\Validators\EventValidator()
            ),
            $converter
        );
        $this->wpReleaseEventService = new Service(
            $reader,
            $writer,
            new WPReleaseEventTransform(
                $idprefix,
                new \SchemaTransformer\Transforms\SplitRowsByOccasion('acf.occasions'),
                new \SchemaTransformer\Transforms\WPReleaseEventTransform\EventFactory(),
                [
                    new \SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators\ApplyName('title.rendered', $pathValueAccessor),
                    new \SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators\ApplyDescription('acf.description', $pathValueAccessor),
                    new \SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators\ApplyStartDate(),
                    new \SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators\ApplyEndDate(),
                    new \SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators\ApplyEventStatus(),
                    new \SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators\ApplyImage(),
                    new \SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators\ApplyKeywords(),
                    new \SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators\ApplyLocationPlace('acf.location_name', 'acf.location.address', 'acf.location.lat', 'acf.location.lng', $pathValueAccessor),
                    new \SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators\ApplyLocationVirtualLocation('acf.meeting_link', 'acf.connect', $pathValueAccessor),
                    new \SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators\ApplyEventAttendanceMode(),
                    new \SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators\ApplyOrganizer(),
                    new \SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators\ApplyTypicalAgeRange(),
                    new \SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators\ApplyOffers(),
                    new \SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators\ApplyAudience(),
                    new \SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators\ApplyIsAccessibleForFree(),
                    new \SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators\ApplyIdAsDefinedTermInKeywords($idprefix)
                ],
                new \SchemaTransformer\Transforms\Validators\EventValidator()
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
