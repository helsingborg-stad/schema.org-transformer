<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\PiosProject\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\Project;

class MapImage extends AbstractPiosProjectMapper
{
    private static $imageExtensions = [
        'jpg', 'jpeg',
        'png',
        'gif',
        'webp',
        'bmp',
        'svg',
        'ico',
        'avif',
        'tif', 'tiff',
        'heic', 'heif',
    ];
    public function map(Project $project, array $data): Project
    {
        return $project->image(
            array_values(
                array_map(
                    fn($dimension) => Schema::imageObject()
                        ->url($dimension['value'] ?? null)
                        ->caption($dimension['name'] ?? null)
                        ->description($dimension['name'] ?? null),
                    array_filter( // select dimensions with image urls
                        $data['customDimensions'] ?? [],
                        fn($dimension) => $this->isImageUrl($dimension['value'] ?? '')
                    )
                )
            )
        );
    }

    public function isImageUrl(?string $url): bool
    {
        $path = parse_url($url, PHP_URL_PATH);

        if (!$path) {
            return false;
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return in_array($extension, self::$imageExtensions, true);
    }
}
