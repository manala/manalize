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
                RequirementType::BINARY(),
                RequirementLevel::REQUIRED(),
                '^1.8.4',
                'Darwin' === php_uname('s') ? ['1.8.5.*', '1.8.6.*', '1.8.7.*'] : ['1.8.5.*', '1.8.6.*'], // /!\ Exclude 1.8.7 for OSX since it is buggy
                'See https://www.vagrantup.com/downloads.html'
            ),
            new Requirement(
                'Landrush',
                'landrush',
                RequirementType::VAGRANT_PLUGIN(),
                RequirementLevel::REQUIRED(),
                '^1.0.0',
                [],
                'See https://github.com/vagrant-landrush/landrush'
            ),
            new Requirement(
                'Ansible',
                'ansible',
                RequirementType::BINARY(),
                RequirementLevel::RECOMMENDED(),
                '^2.1.1',
                [],
                'Required only if you intend to use the deploy role. See http://docs.ansible.com/ansible/intro_installation.html'
            ),
            new Requirement(
                'VirtualBox',
                'VboxManage',
                RequirementType::BINARY(),
                RequirementLevel::RECOMMENDED(),
                '>=5.0.20 <5.0.28',
                [],
                'See https://www.virtualbox.org/wiki/Downloads'
            ),
            new Requirement(
                'Git',
                'git',
                RequirementType::BINARY(),
                RequirementLevel::RECOMMENDED(),
                '^2.0',
                [],
                'Required only if you intend to use the diff command. See https://git-scm.com/book/en/v2/Getting-Started-Installing-Git'
            ),
        ];
    }
}
