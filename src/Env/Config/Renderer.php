<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Env\Config;

use Manala\Manalize\Twig\FilesystemLoader;
use Manala\Manalize\Twig\Lexer;

/**
 * Config' template renderer.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class Renderer
{
    private $twig;

    public function __construct(\Twig_Environment $twig = null)
    {
        if (null === $twig) {
            $twig = new \Twig_Environment(new FilesystemLoader(), [
                'debug' => $debug = '' === \Phar::running(),
                'cache' => $debug ? MANALIZE_DIR.'/var/cache' : MANALIZE_HOME.'/cache',
            ]);
            $twig->setLexer(new Lexer($twig));
        }

        $this->twig = $twig;
    }

    /**
     * Renders a config template.
     *
     * @param Config $config The whole Config for which to dump the template
     *
     * @return string
     *
     * @throws \RuntimeException If the config template is not readable
     */
    public function render(Config $config): string
    {
        $template = $config->getTemplate();

        if (!is_readable($template)) {
            throw new \RuntimeException(sprintf(
                'The template file "%s" is either not readable or doesn\'t exist.',
                $template
            ));
        }

        $context = [];
        foreach ($config->getVars() as $var) {
            $context = array_merge($context, $var->getReplaces());
        }

        return $this->twig->render($template, $context);
    }
}
