<?php

namespace SchemaTransformer\Transforms\WPLegacyEventTransform\SchemaDecorators;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\WPLegacyEventTransform\SchemaDecorators\ApplyOffers;
use Spatie\SchemaOrg\Schema;

class ApplyOffersTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testCanBeCreated(): void
    {
        $this->assertInstanceOf(ApplyOffers::class, new ApplyOffers());
    }

    #[TestDox('applies offer for adult if available')]
    public function testApplyOffersForAdult(): void
    {
        $applyOffers = new ApplyOffers();
        $data        = ['price_adult' => "390"];

        $event = $applyOffers->apply(Schema::event(), $data);

        $this->assertEquals(1, count($event->getProperty('offers')));
        $this->assertEquals(390, $event->getProperty('offers')[0]->getProperty('price'));
        $this->assertEquals('SEK', $event->getProperty('offers')[0]->getProperty('priceCurrency'));
        $this->assertEquals('Standard/Vuxen', $event->getProperty('offers')[0]->getProperty('name'));
    }

    #[TestDox('applies offer for children if available')]
    public function testApplyOffersForChildren(): void
    {
        $applyOffers = new ApplyOffers();
        $data        = ['price_children' => "190"];

        $event = $applyOffers->apply(Schema::event(), $data);

        $this->assertEquals(1, count($event->getProperty('offers')));
        $this->assertEquals(190, $event->getProperty('offers')[0]->getProperty('price'));
        $this->assertEquals('SEK', $event->getProperty('offers')[0]->getProperty('priceCurrency'));
        $this->assertEquals('Barn', $event->getProperty('offers')[0]->getProperty('name'));
    }

    #[TestDox('applies offer for senior if available')]
    public function testApplyOffersForSenior(): void
    {
        $applyOffers = new ApplyOffers();
        $data        = ['price_senior' => "290"];

        $event = $applyOffers->apply(Schema::event(), $data);

        $this->assertEquals(1, count($event->getProperty('offers')));
        $this->assertEquals(290, $event->getProperty('offers')[0]->getProperty('price'));
        $this->assertEquals('SEK', $event->getProperty('offers')[0]->getProperty('priceCurrency'));
        $this->assertEquals('Pensionär', $event->getProperty('offers')[0]->getProperty('name'));
    }

    #[TestDox('applies priceSpecification for seated price ranges')]
    public function testApplyPriceSpecificationForSeatedPriceRanges(): void
    {
        $applyOffers = new ApplyOffers();
        $data        = [
            'price_range' => [
                'seated_minimum_price' => "390",
                'seated_maximum_price' => "490"
            ]
        ];

        $event = $applyOffers->apply(Schema::event(), $data);

        $this->assertEquals(1, count($event->getProperty('offers')));
        $this->assertEquals('Sittplats', $event->getProperty('offers')[0]->getProperty('name'));
        $this->assertEquals(390, $event->getProperty('offers')[0]->getProperty('priceSpecification')->getProperty('minPrice'));
        $this->assertEquals(490, $event->getProperty('offers')[0]->getProperty('priceSpecification')->getProperty('maxPrice'));
    }

    #[TestDox('applies priceSpecification for standing price ranges')]
    public function testApplyPriceSpecificationForStandingPriceRanges(): void
    {
        $applyOffers = new ApplyOffers();
        $data        = [
            'price_range' => [
                'standing_minimum_price' => "290",
                'standing_maximum_price' => "390"
            ]
        ];

        $event = $applyOffers->apply(Schema::event(), $data);

        $this->assertEquals(1, count($event->getProperty('offers')));
        $this->assertEquals('Ståplats', $event->getProperty('offers')[0]->getProperty('name'));
        $this->assertEquals(290, $event->getProperty('offers')[0]->getProperty('priceSpecification')->getProperty('minPrice'));
        $this->assertEquals(390, $event->getProperty('offers')[0]->getProperty('priceSpecification')->getProperty('maxPrice'));
    }
}
