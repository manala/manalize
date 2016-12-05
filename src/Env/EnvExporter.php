<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Env;

use Manala\Manalize\Env\Config\Config;
use Manala\Manalize\Env\Config\Registry;
use Manala\Manalize\Env\Config\Variable\VariableExtractor;

/**
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
final class EnvExporter
{
    private $varExtractor;
    private $configRegistry;

    public function __construct()
    {
        $this->varExtractor = new VariableExtractor();
        $this->configRegistry = new Registry();
    }

    /**
     * Exports a finalized env as raw metadata.
     *
     * @param Env $env
     *
     * @return array Of format ["config alias" => ["variable alias" => ["key" => value, ...]]
     */
    public function export(Env $env): array
    {
        $metadata = [];

        foreach ($env->getConfigs() as $config) {
            $alias = $this->configRegistry->getAliasForClass(get_class($config));
            $metadata[$alias] = $this->exportVars($config);
        }

        return ['name' => $env->getName(), 'configs' => $metadata];
    }

    private function exportVars(Config $config): array
    {
        $exportedVars = [];

        foreach ($config->getVars() as $var) {
            $alias = $this->configRegistry->getAliasForClass(get_class($var));
            $exportedVars[$alias] = $exportedVars[$alias] ?? [];
            $exportedVars[$alias][] = $this->varExtractor->extract($var);
        }

        return $exportedVars;
    }
}
