<?php

namespace RCH\Manalize\Config;

/**
 * Vagrant configuration.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class Vagrant extends Config
{
    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return 'Vagrantfile';
    }
}
