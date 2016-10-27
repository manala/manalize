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

use Composer\Semver\Semver;
use Manala\Manalize\Requirement\Exception\MissingRequirementException;
use Manala\Manalize\Requirement\Factory\HandlerFactoryResolver;
use Manala\Manalize\Requirement\Violation\RequirementViolation;
use Manala\Manalize\Requirement\Violation\RequirementViolationLabelBuilder;
use Manala\Manalize\Requirement\Violation\RequirementViolationList;

/**
 * Service that checks if the current host's environment satisfies a requirement.
 * For example, does the host provide Ansible with the required minimum version ?
 *
 * @author Xavier Roldo <xavier.roldo@elao.com>
 */
class RequirementChecker
{
    private $handlerFactoryResolver;
    private $violationLabelBuilder;

    public function __construct(HandlerFactoryResolver $handlerFactoryResolver, RequirementViolationLabelBuilder $violationLabelBuilder)
    {
        $this->handlerFactoryResolver = $handlerFactoryResolver;
        $this->violationLabelBuilder = $violationLabelBuilder;
    }

    /**
     * Check if the host's environment meets the requirement. If not, adds a RequirementViolation to the
     * violation list.
     *
     * @param Requirement              $requirement
     * @param RequirementViolationList $violationList
     */
    public function check(Requirement $requirement, RequirementViolationList $violationList)
    {
        $handlerFactory = $this->handlerFactoryResolver->getHandlerFactory($requirement);

        try {
            $output = $handlerFactory->getProcessor()->process($requirement->getName());
        } catch (MissingRequirementException $exception) {
            $violationList->addViolation($this->createViolation($requirement));

            return;
        }

        $versionParser = $handlerFactory->getVersionParser();
        $version = $versionParser->getVersion($requirement->getName(), $output);

        if (!SemVer::satisfies($version, $requirement->getSemanticVersion())) {
            $violationList->addViolation($this->createViolation($requirement, $version));
        }
    }

    /**
     * Create a requirement violation based on the requirement and on the current version of the binary if it exists
     * (a null current version means that the binary is not installed at all).
     *
     * @param Requirement $requirement
     * @param string|null $currentVersion
     *
     * @return RequirementViolation
     */
    private function createViolation(Requirement $requirement, $currentVersion = null) : RequirementViolation
    {
        $label = $this->violationLabelBuilder->buildViolationLabel($requirement, $currentVersion);

        return new RequirementViolation($requirement->getName(), $label, $requirement->getLevel(), $requirement->getHelp());
    }
}
