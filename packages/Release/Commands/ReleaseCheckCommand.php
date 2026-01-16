<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Release Check Command
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Release\Commands;

use Release\Services\ReleaseManagerService;
use Release\Services\VersionService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ReleaseCheckCommand extends Command
{
    protected static $defaultName = 'release:check';

    public function __construct(private ReleaseManagerService $releaseManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('release:check')
            ->setDescription('Check for available updates.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Checking for updates...');

        $currentVersion = VersionService::current();
        $io->text("Current Version: <info>{$currentVersion}</info>");

        $update = $this->releaseManager->check();

        if ($update) {
            $io->success("New version available: {$update['version']}");
            $io->text("Description: " . ($update['description'] ?? 'No description'));
            $io->text("Run <comment>php dock release:update</comment> to install.");

            return Command::SUCCESS;
        }

        $io->success('You are on the latest version.');

        return Command::SUCCESS;
    }
}
