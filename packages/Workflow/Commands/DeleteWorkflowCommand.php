<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Delete Workflow Command
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Workflow\Commands;

use Exception;
use Helpers\File\FileSystem;
use Helpers\File\Paths;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DeleteWorkflowCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('workflow:delete')
            ->setDescription('Deletes a workflow class.')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the workflow class')
            ->addArgument('module', InputArgument::OPTIONAL, 'The module name (optional)')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Delete the file without confirmation')
            ->setHelp('This command deletes a workflow from App/src/{Module}/Workflows or App/Workflows.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $name = ucfirst($input->getArgument('name'));
        if (!str_ends_with($name, 'Workflow')) {
            $name .= 'Workflow';
        }
        $module = $input->getArgument('module');
        $force = $input->getOption('force');

        $io->title('Workflow Deleter');

        try {
            if ($module) {
                $moduleName = ucfirst($module);
                $directory = Paths::appSourcePath($moduleName . DIRECTORY_SEPARATOR . 'Workflows');
            } else {
                $directory = Paths::appPath('Workflows');
            }

            $filePath = $directory . DIRECTORY_SEPARATOR . $name . '.php';

            if (!FileSystem::exists($filePath)) {
                $io->error("Workflow {$name} does not exist at {$filePath}");

                return self::FAILURE;
            }

            if (!$force && !$io->confirm("Are you sure you want to delete the workflow {$name}?", false)) {
                $io->note('Deletion cancelled.');

                return self::SUCCESS;
            }

            if (FileSystem::delete($filePath)) {
                $io->success("Workflow {$name} deleted successfully!");

                return self::SUCCESS;
            }

            $io->error("Failed to delete workflow file.");

            return self::FAILURE;
        } catch (Exception $e) {
            $io->error("An error occurred: " . $e->getMessage());

            return self::FAILURE;
        }
    }
}
