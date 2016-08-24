<?php

namespace RCH\Manalize\Config;

/**
 * Object representation of an Ansible configuration.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
abstract class Mixed extends Config
{
    public function __construct(Vars $vars, $target)
    {
        parent::__construct($vars);

        $this->target = $target;
    }

    /**
     * {@inheritdoc}
     */
    public function getTarget()
    {
        return $this->target;
    }
}
