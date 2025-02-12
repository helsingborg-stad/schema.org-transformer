<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\DataSanitizers;

use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\DataSanitizers\SanitizeReachmeeJobPostingLink;

class SanitizeReachmeeJobPostingLinkTest extends TestCase
{
    public function testLinkIsConvertedToApplyLink()
    {
        $data = ["link" => "https://host.com/path/main?site=foo&validator=123&lang=SE&rmpage=job&rmjob=321"];

        $sanitizedData = (new SanitizeReachmeeJobPostingLink())->sanitize($data);

        parse_str(parse_url($sanitizedData['link'], PHP_URL_QUERY), $queryArray);
        $this->assertStringStartsWith('https://host.com/path/apply', $sanitizedData['link']);
        $this->assertEquals('foo', $queryArray['site']);
        $this->assertEquals('SE', $queryArray['lang']);
        $this->assertEquals('321', $queryArray['job_id']);
        $this->assertArrayNotHasKey('rmjob', $queryArray);
        $this->assertArrayNotHasKey('rmpage', $queryArray);
    }
}
