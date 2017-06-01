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

use Manala\Manalize\Env\TemplateName;
use Symfony\Component\Config\Exception\FileLoaderLoadException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Yaml\Parser;

/**
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
final class ManifestLoader extends FileLoader
{
    private const TEMPLATE_PATH = MANALIZE_HOME.'/templates';

    private $templateDir;
    private $parser;

    public function __construct(TemplateName $template = null, FileLocatorInterface $fileLocator = null, Parser $parser = null)
    {
        $this->parser = $parser ?: new Parser();

        if ($template) {
            $this->templateDir = self::TEMPLATE_PATH.'/'.$template->getValue();
        }

        parent::__construct($fileLocator ?: new FileLocator($this->templateDir));
    }

    public function load($resource = null, $type = null): Manifest
    {
        if (!$resource) {
            $resource = $this->templateDir.'/manifest.yaml';
        }

        $this->setCurrentDir(dirname($this->templateDir));

        if (!$this->supports($resource)) {
            throw new FileLoaderLoadException($resource); // TODO better localized exception
        }

        $parsed = $this->parser->parse(file_get_contents($resource));

        if (isset($raw['imports'])) {
            if (!is_array($raw['imports'])) {
                throw new FileLoaderLoadException($resource);
            }

            $directory = dirname($resource);
            foreach ($raw['imports'] as $import) {
                if (!is_string($import)) {
                    throw new \InvalidArgumentException(sprintf('The "imports" key should be a list of valid filename in %s. Check your YAML syntax.', $resource));
                }

                $this->setCurrentDir($directory);
                $this->import($import);
            }

            return $raw;
        }

        return new Manifest($parsed);
    }

    public function supports($resource, $type = null)
    {
        if (!is_string($resource)) {
            return false;
        }

        return is_string($resource) && 'manifest.yaml' === basename($resource);
    }
}
