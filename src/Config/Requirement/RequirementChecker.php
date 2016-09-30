<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Config\Requirement;

use Composer\Semver\Semver;
use Manala\Config\Requirement\Exception\MissingRequirementException;
use Manala\Config\Requirement\Factory\HandlerFactoryResolver;
use Manala\Config\Requirement\Violation\RequirementViolation;
use Manala\Config\Requirement\Violation\RequirementViolationLabelBuilder;
use Manala\Config\Requirement\Violation\RequirementViolationList;

/**
 * Service that checks if the current host's environment satisfies a requirement.
 * For example, does the host provide Ansible with the required minimum version ?
 *
 * @author Xavier Roldo <xavier.roldo@elao.com>
 */
class RequirementChecker
{
    /** @var HandlerFactoryResolver */
    private $handlerFactoryResolver;

    /** @var RequirementViolationLabelBuilder */
    private $violationLabelBuilder;

    public function __construct(
        HandlerFactoryResolver $handlerFactoryResolver,
        RequirementViolationLabelBuilder $violationLabelBuilder
    ) {
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

        $processor = $handlerFactory->getProcessor();
        try {
            $output = $processor->process($requirement->getName());
        } catch (MissingRequirementException $exception) {
            $violationList->addViolation($this->createViolation($requirement));

            return;
        }

        $versionParser = $handlerFactory->getVersionParser();
        $version = $versionParser->getVersion($requirement->getName(), $output);
        if (!SemVer::satisfies($version, $requirement->getSemanticVersion())) {
            $violationList->addViolation($this->createViolation($requirement, $version));

            return;
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
    private function createViolation(Requirement $requirement, $currentVersion = null)
    {
        $label = $this->violationLabelBuilder->buildViolationLabel($requirement, $currentVersion);

        return new RequirementViolation($requirement->getName(), $label, $requirement->getLevel(), $requirement->getHelp());
    }
}
