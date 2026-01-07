<?php

namespace App\RuleEngine\Values;

class ArrayHelper
{
    /**
     * Search for a value in an array using dot notation path.
     * Returns true if found, false otherwise.
     * The matched value is passed by reference.
     *
     * @param  array<mixed>  $array
     */
    public static function search(array $array, string $path, mixed &$match = null): bool
    {
        $keys = explode('.', $path);
        $current = $array;

        foreach ($keys as $key) {
            if (is_array($current) && array_key_exists($key, $current)) {
                $current = $current[$key];
            } elseif (is_object($current) && property_exists($current, $key)) {
                $current = $current->$key;
            } else {
                return false;
            }
        }

        $match = $current;

        return true;
    }

    /**
     * Flatten a multi-dimensional array into dot notation keys.
     *
     * @param  array<mixed>  $array
     * @return array<string, mixed>
     */
    public static function toDot(array $array, string $prefix = ''): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            $newKey = $prefix === '' ? (string) $key : "{$prefix}.{$key}";

            if (is_array($value) && ! empty($value)) {
                $result = array_merge($result, self::toDot($value, $newKey));
            } else {
                $result[$newKey] = $value;
            }
        }

        return $result;
    }
}
