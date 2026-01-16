<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Command to list all registered tenants.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Tenancy\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tenancy\Models\Tenant;

class TenantListCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('tenant:list')
            ->setDescription('List all registered tenants.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Registered Tenants');

        $tenants = Tenant::all();

        if ($tenants->isEmpty()) {
            $io->warning('No tenants found.');

            return Command::SUCCESS;
        }

        $rows = [];
        foreach ($tenants as $tenant) {
            $rows[] = [
                $tenant->id,
                $tenant->name,
                $tenant->subdomain,
                $tenant->status,
                $tenant->db_name,
                $tenant->created_at->toDateTimeString()
            ];
        }

        $io->table(
            ['ID', 'Name', 'Subdomain', 'Status', 'Database', 'Created At'],
            $rows
        );

        return Command::SUCCESS;
    }
}
