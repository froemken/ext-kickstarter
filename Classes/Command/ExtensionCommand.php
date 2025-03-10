<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Command;

use StefanFroemken\ExtKickstarter\Model\Node;
use StefanFroemken\ExtKickstarter\Service\BuildExtensionService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExtensionCommand extends Command
{
    public function __construct(
        readonly private BuildExtensionService $buildExtensionService
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Building Extension</info>');

        return Command::SUCCESS;
    }
}
