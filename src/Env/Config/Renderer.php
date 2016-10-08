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

/**
 * Config' template renderer.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class Renderer
{
    /**
     * Renders a config template.
     *
     * @param Config $config The whole Config for which to dump the template
     *
     * @return string
     */
    public static function render(Config $config)
    {
        $template = $config->getTemplate();

        if (!is_readable($template)) {
            throw new \RuntimeException(sprintf(
                'The template file "%s" is either not readable or doesn\'t exist.',
                $template
            ));
        }

        $vars = $config->getVars();
        $rendered = file_get_contents($template);

        foreach ($vars as $var) {
            $rendered = strtr($rendered, $var->getReplaces());
        }

        return $rendered;
    }
}
