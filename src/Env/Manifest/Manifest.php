<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Env\Manifest;

/**
 * A bag containing all the information for a given template (system & app configuration).
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
final class Manifest
{
    private $attributes;

    /**
     * @param array $attributes The raw defaults
     */
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Gets the value of a given default option.
     *
     * @param string $path The dot path of the option for which to
     *                     return the value
     *
     * @return mixed
     */
    public function get(string $path)
    {
        return $this->doGet($this->attributes, $path);
    }

    /**
     * Checks if a given default option exists.
     *
     * @param string $path The dot path of the option for which to check the existence
     *
     * @return bool
     */
    public function has(string $path): bool
    {
        return (bool) $this->doGet($this->attributes, $path, false);
    }

    /**
     * @param array  $attributes                  The elements to iterate over
     * @param string $path                        The path for which to find the value
     * @param bool   $throwExceptionOnInvalidPath
     *
     * @return mixed
     *
     * @throws \LogicException If the given path cannot be found in the given elements and
     *                         $throwExceptionOnInvalidPath is set to true
     */
    private static function doGet(array $elements, string $path, bool $throwExceptionOnInvalidPath = true)
    {
        $result = $elements;
        $steps = explode('.', $path);

        foreach ($steps as $step) {
            if (!array_key_exists($step, $result)) {
                if (false === $throwExceptionOnInvalidPath) {
                    return false;
                }

                throw self::didYouMean($step, array_keys($result), $path);
            }

            $result = $result[$step];
        }

        return $result;
    }

    private static function didYouMean(string $search, array $possibleMatches, string $fullPath): \LogicException
    {
        $minScore = INF;

        foreach ($possibleMatches as $try) {
            $distance = levenshtein($search, $try);

            if ($distance < $minScore) {
                $guess = $try;
                $minScore = $distance;
            }
        }

        $message = sprintf('Unable to find default for path "%s".', $fullPath);

        if (isset($guess) && $minScore < 3) {
            $message .= sprintf(" Did you mean \"%s\"?\n\n", str_replace($search, $guess, $fullPath));
        } else {
            $message .= sprintf(
                ' Possible values: [ %s ]',
                implode(', ', array_map(function ($match) use ($search, $fullPath) {
                    return str_replace($search, $match, $fullPath);
                }, $possibleMatches))
            );
        }

        return new \LogicException($message);
    }
}
