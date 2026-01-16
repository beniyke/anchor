<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Command to run migrations for valid tenants.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Tenancy\Commands;

use Database\DB;
use Database\Migration\Migrator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tenancy\Models\Tenant;
use Tenancy\TenantManager;
use Throwable;

class TenantMigrateCommand extends Command
{
    private TenantManager $tenantManager;

    public function __construct(TenantManager $tenantManager)
    {
        parent::__construct();
        $this->tenantManager = $tenantManager;
    }

    protected function configure(): void
    {
        $this->setName('tenant:migrate')
            ->setDescription('Run migrations for valid tenants.')
            ->addOption('tenant', null, InputOption::VALUE_OPTIONAL, 'The subdomain of the tenant to migrate.', null)
            ->addOption('fresh', null, InputOption::VALUE_NONE, 'Drop all tables and re-run all migrations.')
            ->addOption('seed', null, InputOption::VALUE_NONE, 'Seed the database after migration.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $tenantSubdomain = $input->getOption('tenant');
        $fresh = (bool) $input->getOption('fresh');
        $seed = (bool) $input->getOption('seed');

        $tenants = $tenantSubdomain
            ? Tenant::where('subdomain', $tenantSubdomain)->get()
            : Tenant::all();

        if ($tenants->isEmpty()) {
            $io->warning('No tenants found to migrate.');

            return Command::SUCCESS;
        }

        $io->title('Running Tenant Migrations');

        foreach ($tenants as $tenant) {
            $io->section("Migrating Tenant: {$tenant->name} ({$tenant->subdomain})");

            try {
                $this->tenantManager->setContext($tenant);
                $connection = DB::connection();
                $path = config('tenancy.database.migrations_path');

                if (!$path || !is_dir($path)) {
                    $io->error("Invalid migrations path configured: {$path}");
                    continue;
                }

                $migrator = new Migrator($connection, $path);

                $results = $migrator->run();

                if (empty($results)) {
                    $io->comment('Nothing to migrate.');
                } else {
                    $io->success('Migrated ' . count($results) . ' files.');
                    foreach ($results as $result) {
                        $io->text(" - " . basename($result['file']));
                    }
                }

                if ($seed) {
                    $io->text('Seeding database...');
                    // Seed logic here (e.g. resolve TenantSeeder and run)

                }
            } catch (Throwable $e) {
                $io->error("Failed to migrate tenant {$tenant->subdomain}: " . $e->getMessage());
            } finally {
                $this->tenantManager->reset();
            }
        }

        return Command::SUCCESS;
    }
}
