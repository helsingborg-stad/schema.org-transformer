<?php

namespace SchemaTransformer\Storage\TypesenseStorage;

class TypesenseDataChunker
{
    public function getChunks(mixed $data, int $chunkSize = 100): array
    {
        if (!is_iterable($data)) {
            return [$data];
        }
        $chunks = [];
        $chunk  = [];
        foreach ($data as $item) {
            $chunk[] = $item;
            if (count($chunk) >= $chunkSize) {
                $chunks[] = $chunk;
                $chunk    = [];
            }
        }
        if (!empty($chunk)) {
            $chunks[] = $chunk;
        }
        return $chunks;
    }
}
