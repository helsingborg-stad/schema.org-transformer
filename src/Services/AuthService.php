<?php

declare(strict_types=1);

namespace SchemaTransformer\Services;

use SchemaTransformer\Interfaces\AbstractAuth;
use SchemaTransformer\Interfaces\AbstractDataWriter;

class AuthService implements AbstractAuth
{
    private AbstractDataWriter $writer;

    public function __construct(AbstractDataWriter $writer)
    {
        $this->writer = $writer;
    }

    public function getToken(string $path, string $clientId, string $clientSecret, string $clientScope): string|false
    {
        $data = $this->writer->write(
            $path,
            http_build_query([
                "client_id"     => $clientId,
                "client_secret" => $clientSecret,
                "grant_type"    => "client_credentials",
                "scope"         => $clientScope
            ]),
        );
        if (false === $data) {
            return false;
        }
        return "Authorization: Bearer " . $data["access_token"];
    }
}
