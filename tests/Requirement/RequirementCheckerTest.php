<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Tests\Requirement;

use Manala\Manalize\Requirement\Exception\MissingRequirementException;
use Manala\Manalize\Requirement\Factory\HandlerFactoryInterface;
use Manala\Manalize\Requirement\Factory\HandlerFactoryResolver;
use Manala\Manalize\Requirement\Processor\AbstractProcessor;
use Manala\Manalize\Requirement\Requirement;
use Manala\Manalize\Requirement\RequirementChecker;
use Manala\Manalize\Requirement\RequirementLevel;
use Manala\Manalize\Requirement\RequirementType;
use Manala\Manalize\Requirement\SemVer\BinaryVersionParser;
use Manala\Manalize\Requirement\Violation\RequirementViolationLabelBuilder;
use Manala\Manalize\Requirement\Violation\RequirementViolationList;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class RequirementCheckerTest extends \PHPUnit_Framework_TestCase
{
    /** @var RequirementChecker */
    private $requirementChecker;

    /** @var HandlerFactoryInterface|ObjectProphecy */
    private $handlerFactory;

    /** @var AbstractProcessor|ObjectProphecy */
    private $processor;

    public function setUp()
    {
        $this->handlerFactory = $this->prophesize(HandlerFactoryInterface::class);
        $this->processor = $this->prophesize(AbstractProcessor::class);
        $this->handlerFactory->getProcessor()->willReturn($this->processor->reveal());
        $handlerFactoryResolver = $this->prophesize(HandlerFactoryResolver::class);
        $handlerFactoryResolver->getHandlerFactory(Argument::any())->willReturn($this->handlerFactory);

        $this->requirementChecker = new RequirementChecker(
            $handlerFactoryResolver->reveal(),
            new RequirementViolationLabelBuilder()
        );
    }

    public function testCheckerShouldCreateAViolationIfRequiredBinaryIsMissing()
    {
        $violationList = new RequirementViolationList();
        $this->assertEmpty($violationList);

        $requirement = new Requirement(
            'Vagrant',
            'vagrant',
            RequirementType::BINARY(),
            RequirementLevel::REQUIRED(),
            '^1.8.4',
            []
        );

        $this->processor->process('vagrant')->willThrow(new MissingRequirementException());

        $this->requirementChecker->check($requirement, $violationList);
        $this->assertCount(1, $violationList);
        $this->assertTrue($violationList->containsRequiredViolations());
        $this->assertFalse($violationList->containsRecommendedViolations());
    }

    public function testCheckerShouldCreateAViolationIfRecommendedBinaryIsMissing()
    {
        $violationList = new RequirementViolationList();

        $requirement = new Requirement(
            'Vagrant',
            'vagrant',
            RequirementType::BINARY(),
            RequirementLevel::RECOMMENDED(),
            '^1.8.4',
            []
        );

        $this->processor->process('vagrant')->willThrow(new MissingRequirementException());

        $this->requirementChecker->check($requirement, $violationList);
        $this->assertCount(1, $violationList);
        $this->assertFalse($violationList->containsRequiredViolations());
        $this->assertTrue($violationList->containsRecommendedViolations());
    }

    public function testCheckerShouldCreateAViolationIfRequiredBinaryHasInsufficientVersion()
    {
        $violationList = new RequirementViolationList();

        $requirement = new Requirement(
            'Vagrant',
            'vagrant',
            RequirementType::BINARY(),
            RequirementLevel::REQUIRED(),
            '^1.8.2',
            []
        );

        $this->processor->process('vagrant')->willReturn('vagrant 1.7.2');
        $this->handlerFactory->getVersionParser()->willReturn(new BinaryVersionParser());
        $this->requirementChecker->check($requirement, $violationList);
        $this->assertCount(1, $violationList);
    }

    public function testCheckerShouldNotCreateAViolationIfBinaryIsOK()
    {
        $violationList = new RequirementViolationList();

        $requirement = new Requirement(
            'Vagrant',
            'vagrant',
            RequirementType::BINARY(),
            RequirementLevel::REQUIRED(),
            '^1.8.2',
            []
        );

        $this->processor->process('vagrant')->willReturn('vagrant 1.8.4');
        $this->handlerFactory->getVersionParser()->willReturn(new BinaryVersionParser());
        $this->requirementChecker->check($requirement, $violationList);
        $this->assertEmpty($violationList);
        $this->assertFalse($violationList->containsRecommendedViolations());
        $this->assertFalse($violationList->containsRequiredViolations());
    }

    public function testCheckerShouldHandleExcludedIntermediaryVersions()
    {
        $violationList = new RequirementViolationList();

        $requirement = new Requirement(
            'Vagrant',
            'vagrant',
            RequirementType::BINARY(),
            RequirementLevel::REQUIRED(),
            '>= 1.8.0 < 1.8.5 || ^1.8.6',
            []
        );

        // Version 1.8.5 should generate a violation
        $this->processor->process('vagrant')->willReturn('vagrant 1.8.5');
        $this->handlerFactory->getVersionParser()->willReturn(new BinaryVersionParser());
        $this->requirementChecker->check($requirement, $violationList);
        $this->assertCount(1, $violationList);

        // Version 1.8.4 should pass
        $violationList = new RequirementViolationList();
        $this->processor->process('vagrant')->willReturn('vagrant 1.8.4');
        $this->requirementChecker->check($requirement, $violationList);
        $this->assertEmpty($violationList);

        // Version 1.8.6 should pass
        $violationList = new RequirementViolationList();
        $this->processor->process('vagrant')->willReturn('vagrant 1.8.6');
        $this->requirementChecker->check($requirement, $violationList);
        $this->assertEmpty($violationList);

        // Version 1.9.* should pass
        $violationList = new RequirementViolationList();
        $this->processor->process('vagrant')->willReturn('vagrant 1.9.2');
        $this->requirementChecker->check($requirement, $violationList);
        $this->assertEmpty($violationList);
    }

    public function testCheckerHandlesConflicts()
    {
        $requirement = new Requirement(
            'Vagrant',
            'vagrant',
            RequirementType::BINARY(),
            RequirementLevel::REQUIRED(),
            '^1.8.4',
            ['1.8.5.*']
        );

        // Version 1.8.5 should generate a violation
        $violationList = new RequirementViolationList();
        $this->processor->process('vagrant')->willReturn('vagrant 1.8.5');
        $this->handlerFactory->getVersionParser()->willReturn(new BinaryVersionParser());
        $this->requirementChecker->check($requirement, $violationList);
        $this->assertCount(1, $violationList);

        // Version 1.8.5.x-dev should generate a violation
        $violationList = new RequirementViolationList();
        $this->processor->process('vagrant')->willReturn('vagrant 1.8.5.x-dev');
        $this->handlerFactory->getVersionParser()->willReturn(new BinaryVersionParser());
        $this->requirementChecker->check($requirement, $violationList);
        $this->assertCount(1, $violationList);

        // Version 1.8.4 should pass
        $violationList = new RequirementViolationList();
        $this->processor->process('vagrant')->willReturn('vagrant 1.8.4');
        $this->requirementChecker->check($requirement, $violationList);
        $this->assertEmpty($violationList);

        // Version 1.8.6 should pass
        $violationList = new RequirementViolationList();
        $this->processor->process('vagrant')->willReturn('vagrant 1.8.6');
        $this->requirementChecker->check($requirement, $violationList);
        $this->assertEmpty($violationList);

        // Version 1.9.* should pass
        $violationList = new RequirementViolationList();
        $this->processor->process('vagrant')->willReturn('vagrant 1.9.2');
        $this->requirementChecker->check($requirement, $violationList);
        $this->assertEmpty($violationList);
    }
}
