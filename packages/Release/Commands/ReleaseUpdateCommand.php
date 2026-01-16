<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Release Update Command
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Release\Commands;

use Exception;
use Release\Services\ReleaseManagerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ReleaseUpdateCommand extends Command
{
    protected static $defaultName = 'release:update';

    public function __construct(private ReleaseManagerService $releaseManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('release:update')
            ->setDescription('Update the application to the latest version.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $update = $this->releaseManager->check();

        if (!$update) {
            $io->success('No updates available.');

            return Command::SUCCESS;
        }

        $io->warning("A new version ({$update['version']}) is available.");
        $io->text("This process will put the application in maintenance mode.");

        if (!$io->confirm('Do you want to proceed?', true)) {
            $io->text('Update cancelled.');

            return Command::SUCCESS;
        }

        try {
            $io->section('Starting update process...');

            $this->releaseManager->update();

            $io->success("Application successfully updated to version {$update['version']}!");

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error("Update failed: " . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
