<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * CLI command to prune old notifications.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Notification\Commands;

use Notification\Services\NotificationManagerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PruneNotificationsCommand extends Command
{
    protected static $defaultName = 'notification:prune';

    public function __construct(
        private readonly NotificationManagerService $notificationManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('notification:prune')
            ->setDescription('Prune old notifications')
            ->addOption(
                'days',
                'd',
                InputOption::VALUE_REQUIRED,
                'How many days of notifications to keep?',
                30
            )
            ->addOption(
                'unread',
                'u',
                InputOption::VALUE_NONE,
                'Also prune unread notifications?'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $days = (int) $input->getOption('days');
        $pruneUnread = (bool) $input->getOption('unread');

        $message = $pruneUnread
            ? "Pruning ALL notifications older than {$days} days..."
            : "Pruning READ notifications older than {$days} days...";

        $io->comment($message);

        $count = $this->notificationManager->prune($days, $pruneUnread);

        $io->success("Successfully pruned {$count} notifications.");

        return Command::SUCCESS;
    }
}
