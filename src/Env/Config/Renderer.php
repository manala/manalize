<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Env\Config;

use Symfony\Component\Yaml\Yaml;

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
        $content = file_get_contents($template);

        if ('yml' === $template->getExtension()) {
            return self::renderYaml($content, $vars);
        }

        $rendered = $content;

        foreach ($vars as $var) {
            $rendered = strtr($rendered, $var->getReplaces());
        }

        return $rendered;
    }

    private static function renderYaml($content, $vars)
    {
        $content = Yaml::parse($content);

        foreach ($content as $k => $v) {
            $rendered[$k] = $v;

            foreach ($vars as $var) {
                if (!is_array($rendered[$k])) {
                    $rendered = array_merge($rendered, array_intersect_key($var->getReplaces(), $rendered));

                    break;
                }

                $rendered[$k] = array_merge($rendered[$k], array_intersect_key($var->getReplaces(), $rendered[$k]));
            }
        }

        return Yaml::dump($rendered);
    }
}
