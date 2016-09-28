<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Command;

use Manala\Config\Requirement\Factory\HandlerFactoryResolver;
use Manala\Config\Requirement\RequirementChecker;
use Manala\Config\Requirement\RequirementRepository;
use Manala\Config\Requirement\Violation\RequirementViolation;
use Manala\Config\Requirement\Violation\RequirementViolationLabelBuilder;
use Manala\Config\Requirement\Violation\RequirementViolationList;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
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
        $output = new SymfonyStyle($input, $output);
        $violationList = new RequirementViolationList();
        $requirementChecker = new RequirementChecker(
            new HandlerFactoryResolver(),
            new RequirementViolationLabelBuilder()
        );

        foreach (RequirementRepository::getRequirements() as $requirement) {
            $output->writeln('Checking '.$requirement->getName());
            $requirementChecker->check($requirement, $violationList);
        }

        if (count($violationList) > 0) {
            foreach ($violationList as $violation) {
                $this->displayViolation($output, $violation);
            }
        }

        if (!$violationList->containsRequiredViolations()) {
            $output->success('Congratulations ! Everything seems OK.');
            if ($violationList->containsRecommendedViolations()) {
                $output->writeln('Yet, some recommendations have been emitted (see above).');
            }
        }
    }

    private function displayViolation(SymfonyStyle $output, RequirementViolation $violation)
    {
        $message = $violation->getLabel();

        if ($help = $violation->getHelp()) {
            $message .= $help;
        }

        return $output->block(
            $message,
            strtoupper($violation->getLevelLabel()),
            $violation->isRequired() ? 'fg=white;bg=red' : 'bg=yellow',
            ' ',
            true
        );
    }
}
