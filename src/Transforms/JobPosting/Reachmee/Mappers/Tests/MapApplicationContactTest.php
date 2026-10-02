<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\MapApplicationContact;

#[CoversClass(MapApplicationContact::class)]
final class MapApplicationContactTest extends TestCase
{
    #[TestDox('jobPosting::applicationContact is mapped from contact_persons')]
    public function testMapsContactPersonsToApplicationContacts(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapApplicationContact(),
            '{
                "contact_persons": [
                    {
                        "position": "Recruiter",
                        "first_name": "Test",
                        "surname": "Testarsson",
                        "email": "test@example.com",
                        "phone": "070123456"
                    }
                ]
            }',
            Schema::jobPosting()->applicationContact([
                Schema::contactPoint()
                    ->contactType('Recruiter')
                    ->name('Test Testarsson')
                    ->email('test@example.com')
                    ->telephone('070123456')
            ])
        );
    }

    #[TestDox('jobPosting::applicationContact is empty when contact_persons is missing')]
    public function testMapsMissingContactPersonsToEmptyList(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapApplicationContact(),
            '{
                "id": 123
            }',
            Schema::jobPosting()->applicationContact([])
        );
    }
}
