<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\Visma;

use Municipio\Schema\Schema;
use SchemaTransformer\Interfaces\AbstractDataTransform;
use SchemaTransformer\Transforms\TransformBase;
use SimpleXMLElement;

/**
 * Transforms Visma Recruit assignments into schema.org JobPosting documents.
 *
 * Input is produced by VismaApi: the list assignment and its detail item as XML.
 */
class VismaJobPostingTransform extends TransformBase implements AbstractDataTransform
{
    public const CREATED_BY = 'municipio://schema.org-transformer/visma-recruit';

    public function __construct(string $idprefix = '')
    {
        parent::__construct($idprefix);
    }

    /**
     * Transform Visma assignments into JobPosting documents.
     *
     * Each entry holds the XML of one list assignment and its detail item, as
     * fetched by VismaApi. No network access happens here.
     *
     * @param array<int, array{assignment: string, item: string}> $data
     *
     * @return array<int, array<string, mixed>>
     */
    public function transform(array $data): array
    {
        $output = [];
        foreach ($data as $entry) {
            $jobPosting = $this->tryTransformAssignment($entry);
            if ($jobPosting !== null) {
                $output[] = $jobPosting;
            }
        }

        return $output;
    }

    /**
     * Map one assignment, or return null when its XML cannot be used.
     *
     * @param array{assignment?: string, item?: string} $entry
     *
     * @return array<string, mixed>|null
     */
    private function tryTransformAssignment(array $entry): ?array
    {
        $previous = libxml_use_internal_errors(true);

        try {
            $assignment = new SimpleXMLElement((string) ($entry['assignment'] ?? ''), LIBXML_NOCDATA | LIBXML_NOWARNING);
            $singleItem = new SimpleXMLElement((string) ($entry['item'] ?? ''), LIBXML_NOCDATA | LIBXML_NOWARNING);

            $localization = $assignment->Localization->AssignmentLoc[0] ?? null;
            $singleLoc    = $singleItem->Localization->AssignmentLoc[0] ?? null;
            if (!$localization instanceof SimpleXMLElement || !$singleLoc instanceof SimpleXMLElement) {
                return null;
            }

            return $this->mapJobPosting($assignment, $localization, $singleItem, $singleLoc);
        } catch (\Throwable) {
            return null;
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
    }


    /**
     * Build the JobPosting document for one assignment.
     *
     * @return array<string, mixed>
     */
    private function mapJobPosting(
        SimpleXMLElement $assignment,
        SimpleXMLElement $localization,
        SimpleXMLElement $singleItem,
        SimpleXMLElement $singleLoc
    ): array {
        $accountName = (string) $assignment->AccountName;
        $ownerDept   = $localization->xpath('Departments/Department[@Type="Owner"]/Name');
        $ownerName   = is_array($ownerDept) && isset($ownerDept[0]) ? (string) $ownerDept[0] : '';

        [$org, $unit] = $this->normalizeArray(
            [
                ['nameorgunit' => $accountName],
                ['nameorgunit' => $ownerName],
            ],
            2,
            ['nameorgunit' => '']
        );

        $county       = ['name' => (string) ($localization->County->Name ?? '')];
        $municipality = ['name' => (string) ($localization->Municipality->Name ?? '')];

        $directApply = $this->xmlPathText(
            $singleItem,
            'ApplicationMethods',
            'ApplicationMethod',
            'ValueXml',
            'web',
            'url'
        );
        $readMoreUrl = trim((string) ($singleItem->ReadMoreUrl ?? ''));
        if ($readMoreUrl === '') {
            $readMoreUrl = trim((string) ($assignment->ReadMoreUrl ?? ''));
        }

        $additionalInfoRaw  = (string) ($singleLoc->AdditionalInfo ?? '');
        $employmentGrade    = (string) ($singleLoc->EmploymentGrade->Name ?? '');
        $employmentDuration = $this->extractEmploymentDurationText($assignment, $singleLoc, $localization);
        $jobStartMerged     = $this->mergeEmploymentJobStart($assignment, $singleItem, $singleLoc, $localization);

        $departmentDescr          = $this->linkifyUrlsInPlainText((string) ($singleLoc->DepartmentDescr ?? ''));
        $workDescrBody            = $this->linkifyUrlsInPlainText((string) ($singleLoc->WorkDescr ?? ''));
        $qualificationsBody       = $this->linkifyUrlsInPlainText((string) ($singleLoc->Qualifications ?? ''));
        $additionalInfoLinked     = $this->linkifyUrlsInPlainText($additionalInfoRaw);
        $occupationClassification = $this->occupationClassificationText($singleLoc);

        $fullDescription  = '<h2>Om arbetsplatsen</h2>';
        $fullDescription .= $departmentDescr;
        $fullDescription .= '<h2>Arbetsuppgifter</h2>';
        $fullDescription .= $workDescrBody;
        $fullDescription .= '<h2>Kvalifikationer</h2>';
        $fullDescription .= $qualificationsBody;
        $fullDescription .= '<h2>Övrig information</h2>';
        $otherInfoParts   = [];
        if ($additionalInfoRaw !== '') {
            $otherInfoParts[] = $additionalInfoLinked;
        }
        if ($occupationClassification !== '') {
            $otherInfoParts[] = '<h3>Beskrivning</h3>' . "\n\n" . $occupationClassification;
        }
        $fullDescription .= implode("\n\n", $otherInfoParts);
        $fullDescription  = $this->formatText($fullDescription);

        $jobPosting = Schema::jobPosting()
            ->identifier($this->formatId((string) $assignment->RefNo))
            ->totalJobOpenings((string) $assignment->NumberOfJobs)
            ->title((string) $localization->AssignmentTitle)
            ->description($fullDescription)
            ->jobStartDate($jobStartMerged)
            ->responsibilities((string) $localization->WorkDescr)
            ->datePosted($this->formatDate((string) $assignment->PublishStartDate))
            ->experienceRequirements((string) $localization->WorkExperiencePrerequisite->Name)
            ->employmentType($employmentGrade !== '' ? $employmentGrade : (string) ($localization->EmploymentGrade->Name ?? ''))
            ->workHours((string) ($localization->EmploymentType->Name ?? ''))
            ->relevantOccupation((string) ($localization->EmploymentType->Name ?? ''))
            ->validThrough($this->formatDate((string) $assignment->ApplicationEndDate))
            ->url($directApply)
            ->directApply($directApply !== '');

        if ($additionalInfoRaw !== '') {
            $jobPosting->specialCommitments($additionalInfoLinked);
        }

        if ($employmentDuration !== '') {
            $jobPosting->setProperty('jobDuration', $employmentDuration);
            $jobPosting->setProperty('employmentDuration', $employmentDuration);
        }

        if ($readMoreUrl !== '') {
            $jobPosting->setProperty('readMoreUrl', $readMoreUrl);
        }

        if (!empty($org['nameorgunit'])) {
            $jobPosting->hiringOrganization(
                Schema::organization()->name($org['nameorgunit'])
            );
        }

        $contacts = $this->getContactPersons($singleItem);
        if ($contacts !== []) {
            $jobPosting->applicationContact($contacts);
        }

        if (!empty($unit['nameorgunit'])) {
            $jobPosting->employmentUnit(
                $this->employmentUnit($unit['nameorgunit'], $county, $municipality, $localization)
            );
        }

        $jobPosting->setProperty('x-created-by', self::CREATED_BY);
        $jobPosting->setProperty('@version', md5(json_encode($jobPosting->toArray())));

        return $jobPosting->toArray();
    }

    /**
     * Occupation area text shown under Övrig information.
     *
     * The additional-ad-info branch reads OccupationClassification[@LevelId="2"].
     * This feed stores that text on the Level 1 classification instead.
     */
    private function occupationClassificationText(SimpleXMLElement $singleLoc): string
    {
        $queries = [
            'OccupationClassifications/OccupationClassification[@LevelId="2"]',
            'OccupationClassifications/OccupationClassification[Level="1"]',
        ];

        foreach ($queries as $query) {
            $text = $this->occupationDescr($singleLoc, $query);
            if ($text !== '') {
                return $this->linkifyUrlsInPlainText($text);
            }
        }

        foreach ($singleLoc->OccupationClassifications->OccupationClassification ?? [] as $classification) {
            $text = trim((string) ($classification->Descr ?? ''));
            if ($text === '') {
                continue;
            }

            return $this->linkifyUrlsInPlainText($text);
        }

        return '';
    }

    /**
     * First non-empty Descr for an occupation xpath.
     */
    private function occupationDescr(SimpleXMLElement $singleLoc, string $query): string
    {
        $nodes = $singleLoc->xpath($query);
        if (!is_array($nodes) || !isset($nodes[0])) {
            return '';
        }

        return trim((string) ($nodes[0]->Descr ?? ''));
    }

    /**
     * Visma may leave EmploymentDuration/Descr empty. Fall back to Name, the list feed, end-date text, or Assignment.EmploymentEndDate.
     */
    private function extractEmploymentDurationText(
        SimpleXMLElement $assignment,
        SimpleXMLElement $singleLoc,
        SimpleXMLElement $listLoc
    ): string {
        $candidates = [
            (string) ($singleLoc->EmploymentDuration->Descr ?? ''),
            (string) ($singleLoc->EmploymentDuration->Name ?? ''),
            (string) ($listLoc->EmploymentDuration->Descr ?? ''),
            (string) ($listLoc->EmploymentDuration->Name ?? ''),
            (string) ($singleLoc->EmploymentEndDateDescr ?? ''),
            (string) ($listLoc->EmploymentEndDateDescr ?? ''),
        ];

        foreach ($candidates as $candidate) {
            $text = trim($candidate);
            if ($text !== '') {
                return $text;
            }
        }

        $endRaw = (string) ($assignment->EmploymentEndDate ?? '');
        $endTs  = strtotime($endRaw);
        if ($endTs === false) {
            return '';
        }

        $endDate = date('Y-m-d', $endTs);
        if ($endDate === '1753-01-01') {
            return '';
        }

        return $endDate;
    }

    /**
     * Merge Assignment.EmploymentStartDate with AssignmentLoc.EmploymentStartDateDescr.
     *
     * Visma uses 1753-01-01 when there is no fixed start date.
     */
    private function mergeEmploymentJobStart(
        SimpleXMLElement $assignment,
        SimpleXMLElement $singleItem,
        SimpleXMLElement $singleLoc,
        SimpleXMLElement $listLoc
    ): string {
        $descr = trim((string) ($singleLoc->EmploymentStartDateDescr ?? ''));
        if ($descr === '') {
            $descr = trim((string) ($listLoc->EmploymentStartDateDescr ?? ''));
        }

        $dateRaw = trim((string) ($singleItem->EmploymentStartDate ?? ''));
        if ($dateRaw === '') {
            $dateRaw = trim((string) ($assignment->EmploymentStartDate ?? ''));
        }

        $datePart = '';
        if ($dateRaw !== '') {
            $startTs = strtotime($dateRaw);
            if ($startTs !== false) {
                $startDate = date('Y-m-d', $startTs);
                if ($startDate !== '1753-01-01') {
                    $datePart = $startDate;
                }
            }
        }

        if ($datePart !== '' && $descr !== '') {
            return $datePart . ' – ' . $descr;
        }

        if ($datePart !== '') {
            return $datePart;
        }

        return $descr;
    }

    /**
     * Contact persons from the assignment item.
     *
     * @return array<int, \Municipio\Schema\ContactPoint>
     */
    private function getContactPersons(SimpleXMLElement $assignment): array
    {
        $contacts     = [];
        $contactNodes = $assignment->Localization->AssignmentLoc->ContactPersons->ContactPerson ?? [];

        foreach ($contactNodes as $contact) {
            $contacts[] = Schema::contactPoint()
                ->contactType((string) $contact->Title)
                ->name((string) $contact->ContactName)
                ->email((string) $contact->Email)
                ->telephone((string) $contact->Telephone);
        }

        return $contacts;
    }

    /**
     * Employment unit with county, municipality and country when the list feed has them.
     */
    private function employmentUnit(
        string $name,
        array $county,
        array $municipality,
        SimpleXMLElement $localization
    ): \Municipio\Schema\Organization {
        $organization = Schema::organization()->name($name);
        if ($county['name'] === '' && $municipality['name'] === '' && (string) $localization->Country->Name === '') {
            return $organization;
        }

        $address = Schema::postalAddress();
        if ($county['name'] !== '') {
            $address->addressRegion($county['name']);
        }
        if ($municipality['name'] !== '') {
            $address->addressLocality($municipality['name']);
        }
        if ((string) $localization->Country->Name !== '') {
            $address->addressCountry((string) $localization->Country->Name);
        }

        return $organization->address($address);
    }

    /**
     * Turn blank lines into paragraphs and remaining line breaks into br elements.
     */
    private function formatText(string $text): string
    {
        $text = str_replace(["\r\n\r\n", "\n\n", "\r\r"], '[[paragraph]]', $text);
        $text = nl2br($text);

        $formattedText = '';
        foreach (explode('[[paragraph]]', $text) as $paragraph) {
            $formattedText .= '<p>' . trim($paragraph) . '</p>';
        }

        return $formattedText;
    }

    /**
     * Wrap bare http(s) URLs in plain text as anchor elements before description HTML is assembled.
     */
    private function linkifyUrlsInPlainText(string $text): string
    {
        if ($text === '') {
            return $text;
        }

        $linked = preg_replace_callback(
            '#https?://[^\s<>"\']+#i',
            static function (array $matches): string {
                $url     = $matches[0];
                $core    = rtrim($url, '.,;:!?)\]');
                $suffix  = $core !== $url ? substr($url, strlen($core)) : '';
                $escaped = htmlspecialchars($core, ENT_QUOTES | ENT_HTML5, 'UTF-8');

                return '<a href="' . $escaped . '">' . $escaped . '</a>'
                    . htmlspecialchars($suffix, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            },
            $text
        );

        return $linked ?? $text;
    }

    /**
     * Pad a list so organization and employment unit can be read by index.
     *
     * @param array<int, array<string, string>>|null $in
     * @param array<string, string>                  $fallback
     *
     * @return array<int, array<string, string>>
     */
    private function normalizeArray(?array $in, int $length, array $fallback): array
    {
        if ($in === null || $in === []) {
            $in = [];
        }

        return array_pad($in, $length, $fallback);
    }
    /**
     * Read a nested element as text. A missing step yields an empty string.
     */
    private function xmlPathText(SimpleXMLElement $node, string ...$path): string
    {
        $current = $node;
        foreach ($path as $name) {
            $next = $current->{$name}[0] ?? null;
            if (!$next instanceof SimpleXMLElement) {
                return '';
            }

            $current = $next;
        }

        return (string) $current;
    }

    /**
     * Format a Visma date as Y-m-d. An unparseable value becomes the unix epoch date, as before.
     */
    private function formatDate(string $date): string
    {
        $timestamp = strtotime($date);
        if ($timestamp === false) {
            return date('Y-m-d', 0);
        }

        return date('Y-m-d', $timestamp);
    }
}
