<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Process Blish Campaigns Command
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Blish\Commands;

use Blish\Services\CampaignProcessorService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

class BlishCampaignProcessCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('blish:process')
            ->setDescription('Process and send scheduled campaigns');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Blish: Process Campaigns');

        try {
            $processor = resolve(CampaignProcessorService::class);
            $io->text('Starting Blish campaign processor...');

            // Logic handled by CampaignProcessorService
            $processed = $processor->processScheduled();

            $io->success("Processed {$processed} campaigns successfully.");

            return Command::SUCCESS;
        } catch (Throwable $e) {
            $io->error('A critical error occurred during campaign processing: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
