<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\MapApplicationContact;

#[CoversClass(MapApplicationContact::class)]
final class MapApplicationContactTest extends TestCase
{
    #[TestDox('jobPosting::applicationContact is mapped from included job-ad contacts')]
    public function testMapsIncludedContactsToApplicationContacts(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapApplicationContact(),
            '{
                "included": [
                    {
                        "type": "job-ad",
                        "attributes": {
                            "contacts": [
                                {
                                    "title": "Recruiter",
                                    "name": "Test Person",
                                    "email": "test.person@example.com",
                                    "phone": "+46 72 123 456"
                                },
                                {
                                    "name": "default contact"
                                }
                            ],
                            "union_contacts": [
                                {
                                    "name": "default union contact"
                                }
                            ]
                        }
                    }
                ]
            }',
            Schema::jobPosting()->applicationContact([
                Schema::contactPoint()
                    ->contactType('Recruiter')
                    ->name('Test Person')
                    ->email('test.person@example.com')
                    ->telephone('+46 72 123 456'),
                Schema::contactPoint()
                    ->contactType('Kontakt')
                    ->name('default contact'),
                Schema::contactPoint()
                    ->contactType('Facklig företrädare')
                    ->name('default union contact'),
            ])
        );
    }

    #[TestDox('jobPosting::applicationContact is empty when there is no included job-ad')]
    public function testMapsMissingIncludedJobAdToEmptyApplicationContacts(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapApplicationContact(),
            '{
                "nothing here": []
            }',
            Schema::jobPosting()->applicationContact([])
        );
    }
}
