<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Env\Metadata;

/**
 * A metadata bag containing all defaults for a given env type.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
final class MetadataBag
{
    private $elements;

    /**
     * @param array $elements The raw metadata
     */
    public function __construct(array $elements)
    {
        $this->elements = $elements;
    }

    /**
     * Gets the value of a given metadata option.
     *
     * @param string $path The dot path of the option for which to
     *                     return the value
     *
     * @return mixed
     */
    public function get($path)
    {
        return $this->doGet($this->elements, $path);
    }

    /**
     * Checks if a given metadata option exists.
     *
     * @param string $path The dot path of the option for which to check the existence
     *
     * @return bool
     */
    public function has($path)
    {
        return (bool) $this->doGet($this->elements, $path, false);
    }

    /**
     * @param array  $elements                    The elements to iterate over
     * @param string $path                        The path for which to find the value
     * @param bool   $throwExceptionOnInvalidPath
     *
     * @return mixed
     *
     * @throws \LogicException If the given path cannot be found in the given elements and
     *                         $throwExceptionOnInvalidPath is set to true
     */
    private function doGet(array $elements, $path, $throwExceptionOnInvalidPath = true)
    {
        $result = $elements;
        $steps = explode('.', $path);

        foreach ($steps as $step) {
            if (!array_key_exists($step, $result)) {
                if (false === $throwExceptionOnInvalidPath) {
                    return false;
                }

                throw $this->didYouMean($step, array_keys($result), $path);
            }

            $result = $result[$step];
        }

        return $result;
    }

    /**
     * Throws a "Did you mean ...?" exception.
     *
     * @param string      $search
     * @param array       $possibleMatches
     * @param string|null $fullPath
     *
     * @return \LogicException
     */
    private function didYouMean($search, array $possibleMatches, $fullPath)
    {
        $minScore = INF;

        foreach ($possibleMatches as $try) {
            $distance = levenshtein($search, $try);

            if ($distance < $minScore) {
                $guess = $try;
                $minScore = $distance;
            }
        }

        $message = sprintf('Unable to find metadata for path "%s".', $fullPath);

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
