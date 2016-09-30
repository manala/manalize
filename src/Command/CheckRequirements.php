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
        $this->configureFormatter($output->getFormatter());

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
                $description = $this->getFormattedViolationDescription($violation);
                $output->writeln($description);
            }
        }

        if (!$violationList->containsRequiredViolations()) {
            $output->writeln('Congratulations ! Everything seems OK.');
            if ($violationList->containsRecommendedViolations()) {
                $output->writeln('Yet, some recommendations have been emitted (see above).');
            }
        }
    }

    /**
     * @param OutputFormatterInterface $formatter
     */
    private function configureFormatter(OutputFormatterInterface $formatter)
    {
        $errorStyle = new OutputFormatterStyle('red');
        $formatter->setStyle('error', $errorStyle);

        $warningStyle = new OutputFormatterStyle('yellow');
        $formatter->setStyle('warning', $warningStyle);
    }

    /**
     * @param RequirementViolation $violation
     *
     * @return string Formatted violation description displayed to user
     */
    private function getFormattedViolationDescription(RequirementViolation $violation)
    {
        $resultPattern = $violation->isRequired() ? '<error>%s</error>' : '<warning>%s</warning>';
        $displayedText = $violation->getLabel();
        $displayedText .= $violation->getHelp() ? ' '.$violation->getHelp() : '';

        return sprintf($resultPattern, $displayedText);
    }
}
