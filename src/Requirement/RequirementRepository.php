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
                '^2.2.1',
                [],
                'See https://www.vagrantup.com/downloads.html'
            ),
            new Requirement(
                'Landrush',
                'landrush',
                RequirementType::VAGRANT_PLUGIN(),
                RequirementLevel::REQUIRED(),
                '^1.3.0',
                [],
                'See https://github.com/vagrant-landrush/landrush'
            ),
            new Requirement(
                'Ansible',
                'ansible',
                RequirementType::BINARY(),
                RequirementLevel::RECOMMENDED(),
                '^2.6.5',
                [],
                'Required only if you intend to use the deploy role. See http://docs.ansible.com/ansible/intro_installation.html'
            ),
            new Requirement(
                'VirtualBox',
                'VboxManage',
                RequirementType::BINARY(),
                RequirementLevel::RECOMMENDED(),
                '^5.2.22',
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
