<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\PreSchool\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\School\PreSchool\Mappers\MapContactPoint;

#[CoversClass(MapContactPoint::class)]
final class MapContactPointTest extends TestCase
{
    #[TestDox('preschool::contactPoint is taken from acf.link_facebook and acf.link_instagrap')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapContactPoint(),
            '{
                "acf":
                    {
                        "link_facebook": "https://facebook.com/skolan",
                        "link_instagram": "https://instagram.com/skolan"
                    }
            }
        ',
            Schema::preschool()
                ->contactPoint([
            Schema::contactPoint()
                ->name('facebook')
                ->contactType('socialmedia')
                ->url('https://facebook.com/skolan'),
            Schema::contactPoint()
                ->name('instagram')
                ->contactType('socialmedia')
                ->url('https://instagram.com/skolan')
            ])
        );
    }
}
