<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Scribe Maintenance Command
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Scribe\Commands;

use Scribe\Services\ScribeManagerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

class ScribeClearCacheCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('scribe:clear')
            ->setDescription('Clear Scribe post cache and refresh indexes');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Scribe: Clear Cache');

        try {
            $io->text('Refreshing post indexes via ScribeManagerService...');

            $manager = resolve(ScribeManagerService::class);
            $count = $manager->clearCache();

            $io->success("Scribe cache cleared for {$count} posts.");

            return Command::SUCCESS;
        } catch (Throwable $e) {
            $io->error('A critical error occurred during Scribe maintenance: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
