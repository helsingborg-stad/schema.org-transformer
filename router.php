<?php

declare(strict_types=1);

namespace SchemaTransformer;

require_once 'vendor/autoload.php';

use SchemaTransformer\IO\HttpReader;
use SchemaTransformer\IO\FileWriter;
use SchemaTransformer\Services\Service;
use SchemaTransformer\Transforms\JobPostTransform;

$options = getopt("", ["transform:", "input:", "output:"]);

$input = $options['input'] ?? null;
$output = $options['output'] ?? null;
$transform = $options['transform'] ?? 'jobpost';

if (!$input || !$output || !$transform) {
    echo "Usage: php router.php --input=<source_path> --output=<output_path> [--transform=jobpost]\n";
    exit(1);
}
switch (strtolower($transform)) {
    case 'jobpost':
        (new Service(
            new HttpReader(),
            new FileWriter(),
            new JobPostTransform()
        ))->execute($input, $output);
        break;
    default:
        printf('Missing transform for (%s)', $transform);
}
