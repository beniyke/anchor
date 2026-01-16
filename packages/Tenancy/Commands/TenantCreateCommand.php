<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Command to create a new tenant with a dedicated database.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Tenancy\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tenancy\Services\TenantProvisioningService;
use Throwable;

class TenantCreateCommand extends Command
{
    private TenantProvisioningService $provisioningService;

    public function __construct(TenantProvisioningService $provisioningService)
    {
        parent::__construct();
        $this->provisioningService = $provisioningService;
    }

    protected function configure(): void
    {
        $this->setName('tenant:create')
            ->setDescription('Create a new tenant with dedicated database.')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the tenant (e.g. Acme Corp)')
            ->addArgument('subdomain', InputArgument::REQUIRED, 'The subdomain for the tenant (e.g. acme)')
            ->addArgument('email', InputArgument::REQUIRED, 'The admin email address')
            ->addArgument('plan', InputArgument::OPTIONAL, 'Subscription plan', 'starter');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $name = (string) $input->getArgument('name');
        $subdomain = (string) $input->getArgument('subdomain');
        $email = (string) $input->getArgument('email');
        $plan = (string) $input->getArgument('plan');

        $io->title('Provisioning New Tenant');

        try {
            $io->text("Creating tenant '{$name}' with subdomain '{$subdomain}'...");

            $tenant = $this->provisioningService->create([
                'name' => $name,
                'subdomain' => $subdomain,
                'email' => $email,
                'plan' => $plan,
            ]);

            $io->success("Tenant created successfully!");
            $io->table(
                ['ID', 'Name', 'Subdomain', 'Database'],
                [[
                    $tenant->id,
                    $tenant->name,
                    $tenant->subdomain,
                    $tenant->db_name
                ]]
            );

            return Command::SUCCESS;
        } catch (Throwable $e) {
            $io->error("Failed to create tenant: " . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
