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
                'vagrant',
                Requirement::TYPE_BINARY,
                RequirementLevel::REQUIRED,
                '1.8.4 || ^1.8.6', // /!\ Exclude vagrant 1.8.5 (Manala incompatible)
                'See https://www.vagrantup.com/downloads.html'
            ),
            new Requirement(
                'landrush',
                Requirement::TYPE_VAGRANT_PLUGIN,
                RequirementLevel::REQUIRED,
                '^1.1.2',
                'See https://github.com/vagrant-landrush/landrush'
            ),
            new Requirement(
                'ansible',
                Requirement::TYPE_BINARY,
                RequirementLevel::RECOMMENDED,
                '^2.1.1',
                'Required only if you intend to use the deploy role. See http://docs.ansible.com/ansible/intro_installation.html'
            ),
        ];
    }
}
