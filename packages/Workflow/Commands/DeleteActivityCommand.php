<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Delete Activity Command
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

class DeleteActivityCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('activity:delete')
            ->setDescription('Deletes a workflow activity class.')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the activity class')
            ->addArgument('module', InputArgument::OPTIONAL, 'The module name (optional)')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Delete the file without confirmation')
            ->setHelp('This command deletes an activity from App/src/{Module}/Activities or App/Activities.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $name = ucfirst($input->getArgument('name'));
        if (!str_ends_with($name, 'Activity')) {
            $name .= 'Activity';
        }
        $module = $input->getArgument('module');
        $force = $input->getOption('force');

        $io->title('Activity Deleter');

        try {
            if ($module) {
                $moduleName = ucfirst($module);
                $directory = Paths::appSourcePath($moduleName . DIRECTORY_SEPARATOR . 'Activities');
            } else {
                $directory = Paths::appPath('Activities');
            }

            $filePath = $directory . DIRECTORY_SEPARATOR . $name . '.php';

            if (!FileSystem::exists($filePath)) {
                $io->error("Activity {$name} does not exist at {$filePath}");

                return self::FAILURE;
            }

            if (!$force && !$io->confirm("Are you sure you want to delete the activity {$name}?", false)) {
                $io->note('Deletion cancelled.');

                return self::SUCCESS;
            }

            if (FileSystem::delete($filePath)) {
                $io->success("Activity {$name} deleted successfully!");

                return self::SUCCESS;
            }

            $io->error("Failed to delete activity file.");

            return self::FAILURE;
        } catch (Exception $e) {
            $io->error("An error occurred: " . $e->getMessage());

            return self::FAILURE;
        }
    }
}
