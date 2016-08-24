<?php

namespace RCH\Manalize\Config;

/**
 * Object representation of an Ansible configuration.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class Ansible extends Config
{
    /**
     * Lazy loads ansible configuration files.
     *
     * {@inheritdoc}
     */
    public function getFiles()
    {
        $originDirectory = $this->getOrigin();

        if (!is_readable($originDirectory)) {
            throw new \InvalidArgumentException('Unable to load an Ansible configuration from directory "%s" as it is either not readable or doesn\'t exist.');
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($originDirectory, \FilesystemIterator::SKIP_DOTS),
             \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $file) {
            yield $file;
        }
    }

    public function getTemplate()
    {
        return $this->getOrigin().'/group_vars/all.yml';
    }

    protected function getPath()
    {
        return 'ansible';
    }
}
