<?php

namespace SchemaTransformer;

use SchemaTransformer\Converters\JSONConverter;
use SchemaTransformer\Converters\JSONLConverter;
use SchemaTransformer\IO\ConsoleWriter;
use SchemaTransformer\IO\FileReader;
use SchemaTransformer\IO\FileWriter;
use SchemaTransformer\IO\HttpReader;
use SchemaTransformer\IO\HttpWriter;
use SchemaTransformer\Services\RuntimeServices;

class App
{
    public static function run(array $options): void
    {
        // Set defaults
        $cmd = (object) array_merge([
            "source" => "",
            "destination" => "",
            "transform" => "jobposting",
            "outputformat" => "json"
        ], $options);

        if (empty($cmd->source)) {
            echo "Usage: php router.php --source=<source_path> [--destination=<destination_path> --transform=<jobposting> --outputformat=<json|jsonl>] --config=<config>\n";
            exit(1);
        }
        $config = !empty($cmd->config) ?
            explode(",", $cmd->config) : [];

        // Check if source is url or file
        $reader = filter_var($cmd->source, FILTER_VALIDATE_URL) ?
            new HttpReader() :
            new FileReader();

        // Check if output to file or screen
        $writer = empty($cmd->destination) ? new ConsoleWriter() : (filter_var($cmd->destination, FILTER_VALIDATE_URL) ?
            new HttpWriter() :
            new FileWriter());

        $converter = $cmd->outputformat === 'jsonl' ?
            new JSONLConverter() :
            new JSONConverter();

        // Wire services
        $services = new RuntimeServices($reader, $writer, $converter, $config);

        // Execute
        $result = false;
        switch (strtolower($cmd->transform)) {
            case 'jobposting':
                $result = $services->getJobPostingService()->execute(
                    $cmd->source,
                    $cmd->destination
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
