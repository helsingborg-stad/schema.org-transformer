<?php

namespace SchemaTransformer\Interfaces;

interface PathValueAccessor
{
    /**
     * Gets a value from the provided array using the specified path.
     *
     * @param array $array The array to search through.
     * @param string $path The path to the value to retrieve.
     * @param mixed $default The default value to return if the path is not found.
     *
     * @return mixed The value at the specified path or the default value if not found.
     */
    public function getValue(array $array, string $path, $default = null);
}
