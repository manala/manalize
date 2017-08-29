<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Tests\Functional;

use Manala\Manalize\Env\Config\Variable\AppName;
use Manala\Manalize\Env\Config\Variable\Dependency\Dependency;
use Manala\Manalize\Env\Config\Variable\Dependency\VersionBounded;
use Manala\Manalize\Env\Config\Variable\VariableHydrator;
use Manala\Manalize\Env\Defaults\DefaultsParser;
use Manala\Manalize\Env\EnvName;
use Manala\Manalize\Handler\Setup;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

/**
 * TestCase.
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    const FIXTURES_DIR = __DIR__.'/../fixtures';

    private static $symfonyStandardCopyPath = null;

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        (new Filesystem())->remove(MANALIZE_TMP_ROOT_DIR);
    }

    public static function tearDownAfterClass()
    {
        self::$symfonyStandardCopyPath = null;
    }

    protected static function getDefaultDependenciesForEnv(EnvName $envType)
    {
        foreach (DefaultsParser::parse($envType)->get('packages') as $name => $configs) {
            if (isset($configs['constraint'])) {
                yield new VersionBounded($name, $configs['enabled'], $configs['default']);

                continue;
            }

            yield new Dependency($name, $configs['enabled']);
        }
    }

    protected static function updateDependencyVersion(\Traversable $dependencies, $name, $version)
    {
        foreach ($dependencies as $dependency) {
            if ($dependency->getName() === $name) {
                yield new VersionBounded($name, $dependency->isEnabled(), $version);

                continue;
            }

            yield $dependency;
        }
    }

    protected static function enableDependencyWithVersion(\Traversable $dependencies, $name, $version = null)
    {
        foreach ($dependencies as $dependency) {
            if ($dependency->getName() === $name) {
                (new VariableHydrator())->hydrate(
                    $dependency,
                    ['enabled' => true, 'version' => $version ?? $dependency->getVersion()]
                );
            }

            yield $dependency;
        }
    }

    protected static function enableDependency(\Traversable $dependencies, $name)
    {
        foreach ($dependencies as $dependency) {
            if ($dependency->getName() === $name) {
                (new VariableHydrator())->hydrate($dependency, ['enabled' => true]);
            }

            yield $dependency;
        }
    }

    protected static function createSymfonyStandardProject($cwd)
    {
        if (null === self::$symfonyStandardCopyPath) {
            self::$symfonyStandardCopyPath = manala_get_tmp_dir('test_case_symfony_standard_app_');

            (new Process('composer create-project symfony/framework-standard-edition:3.1.* . --no-install --no-progress --no-interaction', self::$symfonyStandardCopyPath))
                ->setTimeout(null)
                ->run();
        }

        (new Filesystem())->mirror(self::$symfonyStandardCopyPath, $cwd);
    }

    protected static function manalizeProject($cwd, $appName, EnvName $envType, \Traversable $dependencies = null)
    {
        if (null === $dependencies) {
            $dependencies = Setup::createDefaultDependencySet(DefaultsParser::parse($envType));
        }

        (new Setup($cwd, new AppName($appName), $envType, $dependencies))->handle(function () {
        });
    }

    protected static function createManalizedProject($cwd, $appName = 'dummy.manala', EnvName $envType = null, \Iterator $dependencies = null)
    {
        self::createSymfonyStandardProject($cwd);

        if (null === $envType) {
            $envType = EnvName::SYMFONY();
        }

        self::manalizeProject($cwd, $appName, $envType, $dependencies);
    }
}
