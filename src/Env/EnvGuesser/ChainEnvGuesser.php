<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Env\EnvGuesser;

/**
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class ChainEnvGuesser implements EnvGuesserInterface
{
    private $envGuessers;

    /**
     * @param iterable $envGuessers
     */
    public function __construct($envGuessers = [])
    {
        $this->envGuessers = $envGuessers;
    }

    /**
     * Iterates over guessers until the first that succeeds.
     *
     * {@inheritdoc}
     */
    public function guess(\SplFileInfo $config)
    {
        foreach ($this->getGuessers() as $guesser) {
            if (!$guesser instanceof EnvGuesserInterface) {
                throw new \LogicException(sprintf('Env guessers must implement %s', EnvGuesserInterface::class));
            }

            if ($guesser->supports($config) && $template = $guesser->guess($config)) {
                return $template;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports(\SplFileInfo $config): bool
    {
        return true;
    }

    private function getGuessers()
    {
        if ($this->envGuessers) {
            return $this->envGuessers;
        }

        yield new ComposerEnvGuesser();
    }
}
