<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms;

use DateTime;
use SchemaTransformer\Interfaces\AbstractDataTransform;
use Municipio\Schema\Schema;

abstract class SponsorBaseTransform extends TransformBase implements AbstractDataTransform
{
    public function __construct(string $idprefix)
    {
        parent::__construct($idprefix);
    }
    protected function transformDateTime(?string $date, ?string $time): ?DateTime
    {
        return DateTime::createFromFormat(
            'Y-m-d H:i:s',
            trim($date . ' ' . $time)
        ) ?? null;
    }
    protected function transformEvent(?array $acf): SponsorDemand
    {
        $date = $this->transformDateTime($acf['date'], $acf['time']);

        return (new SponsorDemand())
            ->description($acf['description'] ?? null)
            ->startDate($date ? $date->format('Y-m-d\TH:i:s') : '');
    }

    protected function transformOrganization(?array $acf): \Municipio\Schema\Organization
    {
        return Schema::Organization()
            ->name($acf['organization_name'] ?? null)
            ->description($acf['organization_description'] ?? null)
            ->url($acf['organization_url'] ?? null)
            ->taxID($acf['organization_number'] ?? null);
    }
    protected function transformContactPoint(?array $acf): \Municipio\Schema\ContactPoint
    {
        return Schema::ContactPoint()
            ->name($acf['organization_contact'] ?? null)
            ->role($acf['organization_contact_role'] ?? null)
            ->email($acf['organization_email'] ?? null)
            ->telephone($acf['organization_phone'] ?? null);
    }
    protected function transformOffer(?array $acf): SponsorOffer
    {
        $date = $this->transformDateTime($acf['due_date'], $acf['due_time']);

        return new SponsorOffer()
            ->availabilityEnds($date ? $date->format('Y-m-d\TH:i:s') : '')
            ->category($this->transformCategories($acf['resources'] ?? null));
    }
    protected function transformImage(?array $acf): ?\Municipio\Schema\ImageObject
    {
        $img = $acf['image'] ?? [];

        return Schema::imageObject()
            ->url($img['url'] ?? null)
            ->name($img['title'] ?? null)
            ->description($img['alt'] ?? null);
    }
    protected function transformCategories(?array $acf): array
    {
        $categories = $acf['resources'] ?? [];
        foreach ($acf as &$row) {
            $categories[] = $row['name'];
        }
        return $categories;
    }
    protected function transformKeywords(string $name, ?array $acf): array
    {
        $activities = [];
        foreach ($acf['activities'] ?? [] as &$row) {
            $activities[] = Schema::definedTerm()
                ->name($row['name'] ?? '')
                ->inDefinedTermSet(Schema::definedTermSet()->name($name));
        }
        return $activities;
    }
    protected function transformLocation(?array $acf): ?\Municipio\Schema\Place
    {
        $location = $acf['location'] ?? null;

        return !is_null($location) ?
            Schema::Place()
            ->address(Schema::PostalAddress()
            ->streetAddress($location['name'] ?? null)
            ->addressLocality($location['city'] ?? null)
            ->addressRegion($location['state'] ?? null)
            ->postalCode($location['post_code'] ?? null)
            ->addressCountry($location['country'] ?? null))->description($location['address'] ?? null) : null;
    }

    protected function transformDemand(?array $acf): ?\Municipio\Schema\Demand
    {
        return Schema::Demand()
            ->description($acf['requirements'] ?? null);
    }
}
