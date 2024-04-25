<?php

namespace SchemaTransformer;

use SchemaTransformer\IO\ConsoleWriter;
use SchemaTransformer\IO\FileReader;
use SchemaTransformer\IO\FileWriter;
use SchemaTransformer\IO\HttpReader;
use SchemaTransformer\Services\RuntimeServices;

class App
{
    public static function run(array $options): void
    {
        // Set defaults
        $cmd = (object) array_merge([
            "source" => "",
            "destination" => "",
            "transform" => "jobposting"
        ], $options);

        if (empty($cmd->source)) {
            echo "Usage: php router.php --source=<source_path> [--destination=<destination_path> --transform=<jobposting>]\n";
            exit(1);
        }
        // Check if source is url or file
        $reader = filter_var($cmd->source, FILTER_VALIDATE_URL) ?
            new HttpReader() :
            new FileReader();

        // Check if output to file or screen
        $writer = empty($cmd->destination) ?
            new ConsoleWriter() :
            new FileWriter();

        // Wire services
        $services = new RuntimeServices($reader, $writer);

        // Execute
        $result = false;
        switch (strtolower($cmd->transform)) {
            case 'jobposting':
                $result = $services->getJobPostingService()->execute($cmd->source, $cmd->destination);
                break;
            default:
                printf('Missing transform for (%s)', $cmd->transform);
                break;
        }
        if (!$result) {
            print("Transform FAILED");
            exit(1);
        }
    }
}
