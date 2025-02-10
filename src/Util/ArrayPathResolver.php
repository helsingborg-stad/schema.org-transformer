<?php

namespace SchemaTransformer\Util;

use SchemaTransformer\Interfaces\PathValueAccessor;

class ArrayPathResolver implements PathValueAccessor
{
    /**
     * @inheritDoc
     */
    public function getValue(array $array, string $path, $default = null)
    {
        if (is_string($path)) {
            $path = explode('.', $path);
        }

        $current = $array;

        foreach ($path as $key) {
            if (!is_array($current) || !array_key_exists($key, $current)) {
                return $default;
            }

            $current = $current[$key];
        }

        return $current;
    }
}
