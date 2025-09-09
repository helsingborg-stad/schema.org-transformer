<?php

namespace SchemaTransformer;

use Typesense\Client as TypesenseClient;
use SchemaTransformer\Converters\JSONConverter;
use SchemaTransformer\Converters\JSONLConverter;
use SchemaTransformer\IO\ConsoleWriter;
use SchemaTransformer\IO\FileReader;
use SchemaTransformer\IO\FileWriter;
use SchemaTransformer\IO\HttpReader;
use SchemaTransformer\IO\HttpStatusCodeReader;
use SchemaTransformer\IO\HttpWriter;
use SchemaTransformer\Loggers\NullLogger;
use SchemaTransformer\Loggers\TerminalLogger;
use SchemaTransformer\Paginators\GetParamPaginator;
use SchemaTransformer\Paginators\NullPaginator;
use SchemaTransformer\Paginators\WordpressPaginator;
use SchemaTransformer\Services\AuthService;
use SchemaTransformer\Services\RuntimeServices;

class App
{
    public static function run(array $options): void
    {
        // Set defaults
        $cmd = (object) array_merge([
            "source"           => "",
            "sourceheaders"    => "Content-Type: application/json",
            "paginator"        => "",
            "output"           => "",
            "outputheaders"    => "Content-Type: application/json",
            "outputformat"     => "json",
            "transform"        => "jobposting",
            "idprefix"         => "",
            "authpath"         => "",
            "authclientid"     => "",
            "authclientsecret" => "",
            "authscope"        => "",
            "logger"           => "terminal",
            "typesense_apikey" => "",
            "typesense_host"   => "",
            "typesense_port"   => "",
        ], $options);

        if (empty($cmd->source)) {
            echo <<<TEXT
                Usage: php router.php

                Input settings
                 --source <file|url>            Input file or URL
                 --sourceheaders <headers>      Comma separated HTTP headers when source is a URL
                 --paginator <adapter>          The name of a pagination adapter to use (if any)
                                                - wordpress

                Output settings
                 --output <file|url>            Output file or URL
                 --outputheaders <headers>      Comma separated HTTP headers when output is a URL
                 --outputformat <json|jsonl>    Output format

                Transformation settings
                 --transform <name>             Name of transform to apply
                                                - jobposting
                                                - stratsys
                                                - wp_legacy_event
                                                - wp_release_event
                                                - wp_exhibition_event
                                                - elementary_school
                --idprefix                      prefix to avoid collision between items from multiple sources
                 
                OAuth authentication parameters (Applicable for source only)
                 --authpath <url>               URL of token service
                 --authclientid <string>        Client id 
                 --authclientsecret <string>    Client secret
                 --authscope <string>           Client scope

                Logging settings
                 --logger <terminal|none>    Logger to use
                
                Typesense connection (optional, used for some transforms)
                 --typesense_apikey <string>    Typesense API key
                 --typesense_host <string>      Typesense host
                 --typesense_port <int>         Typesense port (default 443)

                TEXT;
            exit(1);
        }

        // Split HTTP headers
        $sourceheaders = !empty($cmd->sourceheaders) ?
            explode(",", $cmd->sourceheaders) : [];
        $outputheaders = !empty($cmd->outputheaders) ?
            explode(",", $cmd->outputheaders) : [];

        $logger = match (strtolower($cmd->logger)) {
            'terminal' => new TerminalLogger(),
            default => new NullLogger()
        };

        // Authenticate
        if (filter_var($cmd->authpath, FILTER_VALIDATE_URL)) {
            $token = (new AuthService(
                new HttpWriter(["Content-Type: application/x-www-form-urlencoded"])
            ))->getToken($cmd->authpath, $cmd->authclientid, $cmd->authclientsecret, $cmd->authscope);

            $sourceheaders[] = $token;
        }

        // Typesense - convenience utility for merged indexing
        $typesenseClient = !empty($cmd->typesense_apikey) && !empty($cmd->typesense_host) ? new TypesenseClient([
            'api_key'                    => $cmd->typesense_apikey,
            'nodes'                      => [
                [
                    'host'     => $cmd->typesense_host,
                    'port'     => !empty($cmd->typesense_port) ? $cmd->typesense_port : 443,
                    'protocol' => 'https'
                ]
            ],
            'connection_timeout_seconds' => 2
        ]) : null;

        switch (strtolower($cmd->paginator)) {
            case 'wordpress':
                $paginator = new WordpressPaginator();
                break;
            case 'get-param':
                $paginator = new GetParamPaginator('page', new HttpStatusCodeReader());
                break;
            default:
                $paginator = new NullPaginator();
                break;
        };

        // Check if source is url or file
        $reader = filter_var($cmd->source, FILTER_VALIDATE_URL) ?
            new HttpReader($paginator, $logger, $sourceheaders) :
            new FileReader();

        // Check if output to file or screen
        $writer = empty($cmd->output) ? new ConsoleWriter() : (filter_var($cmd->output, FILTER_VALIDATE_URL) ?
            new HttpWriter($outputheaders) :
            new FileWriter());

        $converter = $cmd->outputformat === 'jsonl' ?
            new JSONLConverter() :
            new JSONConverter();

            // Wire services
        $services = new RuntimeServices($reader, $writer, $converter, $cmd->idprefix, $typesenseClient);

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
            case 'wp_legacy_event':
                $result = $services->getWPLegacyEventService()->execute(
                    $cmd->source,
                    $cmd->output
                );
                break;
            case 'wp_release_event':
                $result = $services->getWPReleaseEventService()->execute(
                    $cmd->source,
                    $cmd->output
                );
                break;
            case 'wp_exhibition_event':
                $result = $services->getWPExhibitionEventService()->execute(
                    $cmd->source,
                    $cmd->output
                );
                break;
            case 'elementary_school':
                $result = $services->getElementarySchoolService($typesense)->execute(
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
