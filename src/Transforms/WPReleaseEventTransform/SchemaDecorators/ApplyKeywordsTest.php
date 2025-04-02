<?php

namespace SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators\ApplyKeywords;
use Municipio\Schema\Event;
use Municipio\Schema\Schema;

class ApplyKeywordsTest extends TestCase
{
    private ApplyKeywords $decorator;

    protected function setUp(): void
    {
        $this->decorator = new ApplyKeywords();
    }

    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated()
    {
        $this->assertInstanceOf(ApplyKeywords::class, $this->decorator);
    }

    #[TestDox('terms are added as keywords')]
    public function testOrganizerIsAddedToSchemaIfAvailableInData()
    {
        $event = new Event();
        $data  = [
            '_embedded' => [
                'wp:term' => [
                    [
                        [
                            'name'     => 'Test Term',
                            'taxonomy' => 'test-taxonomy'
                        ]
                    ]
                ],
            ],
        ];

        $event    = $this->decorator->apply($event, $data);
        $keywords = $event->getProperty('keywords');

        $this->assertIsArray($keywords);
        $this->assertCount(1, $keywords);
        $this->assertEquals('Test Term', $keywords[0]->getProperty('name'));
        $this->assertEquals('test-taxonomy', $keywords[0]->getProperty('inDefinedTermSet')->getProperty('name'));
    }

    #[TestDox('terms are appended to keywords if already set')]
    public function testOrganizerIsAppendedToSchemaIfAvailableInData()
    {
        $event = new Event();
        $event->keywords([
            Schema::definedTerm()
                ->name('Already Set')
                ->inDefinedTermSet(Schema::definedTermSet()->name('already-set-taxonomy'))
        ]);

        $data = [
            '_embedded' => [
                'wp:term' => [
                    [
                        [
                            'name'     => 'Test Term 2',
                            'taxonomy' => 'test-taxonomy-2'
                        ]
                    ]
                ],
            ],
        ];

        $event    = $this->decorator->apply($event, $data);
        $keywords = $event->getProperty('keywords');

        $this->assertIsArray($keywords);
        $this->assertCount(2, $keywords);
        $this->assertEquals('Already Set', $keywords[0]->getProperty('name'));
        $this->assertEquals('already-set-taxonomy', $keywords[0]->getProperty('inDefinedTermSet')->getProperty('name'));
        $this->assertEquals('Test Term 2', $keywords[1]->getProperty('name'));
        $this->assertEquals('test-taxonomy-2', $keywords[1]->getProperty('inDefinedTermSet')->getProperty('name'));
    }
}
