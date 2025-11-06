<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\ElementarySchool\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\School\ElementarySchool\Mappers\MapEmployee;

#[CoversClass(MapEmployee::class)]
final class MapEmployeeTest extends TestCase
{
    #[TestDox('elementarySchool::employee is taken from employee')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEmployee(),
            '{
                "employee": [
                    {
                        "job_title": "Administrativ samordnare",
                        "name": "Test person",
                        "email": "test.person@example.com",
                        "telephone": "123-456789",
                        "image": {
                            "ID": 0,
                            "url": "https://skolan.se/testperson.jpg",
                            "alt": "alternativ text",
                            "name": "testperson.jpg",
                            "caption": "Porträtt av Test Person"
                        }
                    }
                ]
            }',
            Schema::elementarySchool()->employee([
            Schema::person()
            ->name('Test person')
            ->jobTitle('Administrativ samordnare')
            ->email('test.person@example.com')
            ->telephone('123-456789')
            ->image(Schema::imageObject()
            ->name('testperson.jpg')
            ->caption('Porträtt av Test Person')
            ->description('alternativ text')
            ->url('https://skolan.se/testperson.jpg'))
            ])
        );
    }
}
