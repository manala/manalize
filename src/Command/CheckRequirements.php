<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Command;

use Manala\Manalize\Requirement\Factory\HandlerFactoryResolver;
use Manala\Manalize\Requirement\RequirementChecker;
use Manala\Manalize\Requirement\RequirementRepository;
use Manala\Manalize\Requirement\Violation\RequirementViolation;
use Manala\Manalize\Requirement\Violation\RequirementViolationLabelBuilder;
use Manala\Manalize\Requirement\Violation\RequirementViolationList;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Checks if the host's environment meets Manala's requirements (Vagrant, Ansible, etc.).
 *
 * @author Xavier Roldo <xavier.roldo@elao.com>
 */
class CheckRequirements extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('check:requirements')
            ->setDescription("Checks if your host's environment meets Manala's requirements (Vagrant, Ansible, etc.)")
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $violationList = new RequirementViolationList();
        $requirementChecker = new RequirementChecker(
            new HandlerFactoryResolver(),
            new RequirementViolationLabelBuilder()
        );

        $io->newLine();

        foreach (RequirementRepository::getRequirements() as $requirement) {
            $io->writeln('Checking '.$requirement->getLabel());
            $requirementChecker->check($requirement, $violationList);
        }

        if (count($violationList) > 0) {
            foreach ($violationList as $violation) {
                $this->displayViolation($io, $violation);
            }
        }

        if (!$violationList->containsRequiredViolations()) {
            $io->success('Congratulations! Everything seems OK.');
            if ($violationList->containsRecommendedViolations()) {
                $io->note('Yet, some recommendations have been emitted (see above).');
            }
        }
    }

    private function displayViolation(SymfonyStyle $io, RequirementViolation $violation)
    {
        $message = $violation->getLabel();
        $violatedRequirement = $violation->getViolatedRequirement();

        if ($help = $violatedRequirement->getHelp()) {
            $message .= "\n$help";
        }

        return $io->block(
            $message,
            strtoupper($violatedRequirement->getLevel()),
            $violatedRequirement->isRequired() ? 'fg=white;bg=red' : 'fg=black;bg=yellow',
            ' ',
            true
        );
    }
}
