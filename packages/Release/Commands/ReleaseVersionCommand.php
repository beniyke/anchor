<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Release Version Command
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Release\Commands;

use Release\Services\VersionService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ReleaseVersionCommand extends Command
{
    protected static $defaultName = 'release:current-version';

    public function __construct()
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('release:current-version')
            ->setDescription('Display the current application version.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->text(VersionService::current());

        return Command::SUCCESS;
    }
}
