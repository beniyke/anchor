<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Create Workflow Command
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
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateWorkflowCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('workflow:create')
            ->setDescription('Creates a new workflow class.')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the workflow class')
            ->addArgument('module', InputArgument::OPTIONAL, 'The module name (optional for shared workflows)')
            ->setHelp('This command creates a new workflow in App/src/{Module}/Workflows or App/Workflows if no module is specified.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $name = ucfirst($input->getArgument('name'));
        if (!str_ends_with($name, 'Workflow')) {
            $name .= 'Workflow';
        }
        $module = $input->getArgument('module');

        $io->title('Workflow Generator');

        try {
            if ($module) {
                $moduleName = ucfirst($module);
                $directory = Paths::appSourcePath($moduleName . DIRECTORY_SEPARATOR . 'Workflows');
                $namespace = "App\\{$moduleName}\Workflows";

                if (!FileSystem::isDir(Paths::appSourcePath($moduleName))) {
                    $io->error("Module {$moduleName} does not exist.");

                    return self::FAILURE;
                }
            } else {
                $directory = Paths::appPath('Workflows');
                $namespace = "App\Workflows";
            }

            $filePath = $directory . DIRECTORY_SEPARATOR . $name . '.php';

            if (FileSystem::exists($filePath)) {
                $io->error("Workflow {$name} already exists at {$filePath}");

                return self::FAILURE;
            }

            if (!FileSystem::isDir($directory)) {
                FileSystem::mkdir($directory, 0755, true);
            }

            $stubPath = Paths::systemPath('Workflow' . DIRECTORY_SEPARATOR . 'Stubs' . DIRECTORY_SEPARATOR . 'workflow.php.stub');
            if (!FileSystem::exists($stubPath)) {
                $io->error("Generator stub not found at {$stubPath}");

                return self::FAILURE;
            }

            $content = FileSystem::get($stubPath);
            $content = str_replace(
                ['{namespace}', '{classname}'],
                [$namespace, $name],
                $content
            );

            if (FileSystem::put($filePath, $content)) {
                $io->success("Workflow {$name} created successfully!");
                $io->text("File: {$filePath}");

                return self::SUCCESS;
            }

            $io->error("Failed to create workflow file.");

            return self::FAILURE;
        } catch (Exception $e) {
            $io->error("An error occurred: " . $e->getMessage());

            return self::FAILURE;
        }
    }
}
