<?php

declare(strict_types=1);

namespace SchemaTransformer\IO\V2;

/**
 * HTTP reader that passes an XML body through as raw content.
 */
class XmlHttpReader extends HttpReader
{
    /**
     * Keep the XML body intact for transforms that parse it themselves.
     *
     * @param string $body Raw response body.
     *
     * @return array{content: string}
     */
    protected function decodeBody(string $body): array
    {
        return ['content' => $body];
    }
}
