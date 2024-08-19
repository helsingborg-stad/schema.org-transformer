<?php

namespace SchemaTransformer;

use SchemaTransformer\Converters\JSONConverter;
use SchemaTransformer\Converters\JSONLConverter;
use SchemaTransformer\IO\ConsoleWriter;
use SchemaTransformer\IO\FileReader;
use SchemaTransformer\IO\FileWriter;
use SchemaTransformer\IO\HttpReader;
use SchemaTransformer\IO\HttpWriter;
use SchemaTransformer\Services\AuthService;
use SchemaTransformer\Services\RuntimeServices;

class App
{
    public static function run(array $options): void
    {
        // Set defaults
        $cmd = (object) array_merge([
            "source" => "",
            "sourceheaders" => "Content-Type: application/json",
            "output" => "",
            "outputheaders" => "Content-Type: application/json",
            "outputformat" => "json",
            "transform" => "jobposting",
            "authpath" => "",
            "authclientid" => "",
            "authclientsecret" => "",
            "authscope" => ""
        ], $options);

        if (empty($cmd->source)) {
            echo <<<TEXT
                Usage: php router.php

                Input settings
                 --source <file|url>            Input file or URL
                 --sourceheaders <headers>      Comma separated HTTP headers when source is a URL

                Output settings
                 --output <file|url>            Output file or URL
                 --outputheaders <headers>      Comma separated HTTP headers when output is a URL
                 --outputformat <json|jsonl>    Output format

                Transformation settings
                 --transform <jobposting>       Name of transform to apply 
                
                 OAuth authentication parameters (Applicable for source only)
                 --authpath <url>               URL of token service
                 --authclientid <string>        Client id 
                 --authclientsecret <string>    Client secret
                 --authscope <string>           Client scope

                TEXT;
            exit(1);
        }

        // Split HTTP headers
        $sourceheaders = !empty($cmd->sourceheaders) ?
            explode(",", $cmd->sourceheaders) : [];
        $outputheaders = !empty($cmd->outputheaders) ?
            explode(",", $cmd->outputheaders) : [];

        // Authenticate
        if (filter_var($cmd->authpath, FILTER_VALIDATE_URL)) {
            $token = (new AuthService(
                new HttpWriter(["Content-Type: application/x-www-form-urlencoded"])
            ))->getToken($cmd->authpath, $cmd->authclientid, $cmd->authclientsecret, $cmd->authscope);

            $sourceheaders[] = $token;
        }

        // Check if source is url or file
        $reader = filter_var($cmd->source, FILTER_VALIDATE_URL) ?
            new HttpReader($sourceheaders) :
            new FileReader();

        // Check if output to file or screen
        $writer = empty($cmd->output) ? new ConsoleWriter() : (filter_var($cmd->output, FILTER_VALIDATE_URL) ?
            new HttpWriter($outputheaders) :
            new FileWriter());

        $converter = $cmd->outputformat === 'jsonl' ?
            new JSONLConverter() :
            new JSONConverter();

        // Wire services
        $services = new RuntimeServices($reader, $writer, $converter);

        // Execute
        $result = false;
        switch (strtolower($cmd->transform)) {
            case 'jobposting':
                $result = $services->getJobPostingService()->execute(
                    $cmd->source,
                    $cmd->output
                );
                break;
            case 'stratsys':
                $result = $services->getStratsysService()->execute(
                    $cmd->source,
                    $cmd->output
                );
                break;
            default:
                printf('Missing transform for (%s)\n', $cmd->transform);
                break;
        }
        if (!$result) {
            print("Transform FAILED\n");
            exit(1);
        }
    }
}
