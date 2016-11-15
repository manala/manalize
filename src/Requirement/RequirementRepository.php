<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Requirement;

use Manala\Manalize\Requirement\Common\RequirementLevel;

class RequirementRepository
{
    /**
     * @return Requirement[]
     */
    public static function getRequirements(): array
    {
        return [
            new Requirement(
                'Vagrant',
                'vagrant',
                Requirement::TYPE_BINARY,
                RequirementLevel::REQUIRED,
                '^1.8.4',
                'Darwin' === php_uname('s') ? ['1.8.5.*', '1.8.6.*', '1.8.7.*'] : ['1.8.5.*', '1.8.6.*'], // /!\ Exclude 1.8.7 for OSX since it is buggy
                'See https://www.vagrantup.com/downloads.html'
            ),
            new Requirement(
                'Landrush',
                'landrush',
                Requirement::TYPE_VAGRANT_PLUGIN,
                RequirementLevel::REQUIRED,
                '^1.0.0',
                [],
                'See https://github.com/vagrant-landrush/landrush'
            ),
            new Requirement(
                'Ansible',
                'ansible',
                Requirement::TYPE_BINARY,
                RequirementLevel::RECOMMENDED,
                '^2.1.1',
                [],
                'Required only if you intend to use the deploy role. See http://docs.ansible.com/ansible/intro_installation.html'
            ),
            new Requirement(
                'VirtualBox',
                'VboxManage',
                Requirement::TYPE_BINARY,
                RequirementLevel::RECOMMENDED,
                '>=5.0.20 <5.0.28',
                [],
                'See https://www.virtualbox.org/wiki/Downloads'
            ),
        ];
    }
}
