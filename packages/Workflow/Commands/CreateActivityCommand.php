<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Create Activity Command
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

class CreateActivityCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('activity:create')
            ->setDescription('Creates a new workflow activity class.')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the activity class')
            ->addArgument('module', InputArgument::OPTIONAL, 'The module name (optional for shared activities)')
            ->setHelp('This command creates a new activity in App/src/{Module}/Activities or App/Activities if no module is specified.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $name = ucfirst($input->getArgument('name'));
        if (!str_ends_with($name, 'Activity')) {
            $name .= 'Activity';
        }
        $module = $input->getArgument('module');

        $io->title('Activity Generator');

        try {
            if ($module) {
                $moduleName = ucfirst($module);
                $directory = Paths::appSourcePath($moduleName . DIRECTORY_SEPARATOR . 'Activities');
                $namespace = "App\\{$moduleName}\Activities";

                if (!FileSystem::isDir(Paths::appSourcePath($moduleName))) {
                    $io->error("Module {$moduleName} does not exist.");

                    return self::FAILURE;
                }
            } else {
                $directory = Paths::appPath('Activities');
                $namespace = "App\Activities";
            }

            $filePath = $directory . DIRECTORY_SEPARATOR . $name . '.php';

            if (FileSystem::exists($filePath)) {
                $io->error("Activity {$name} already exists at {$filePath}");

                return self::FAILURE;
            }

            if (!FileSystem::isDir($directory)) {
                FileSystem::mkdir($directory, 0755, true);
            }

            $stubPath = Paths::systemPath('Workflow' . DIRECTORY_SEPARATOR . 'Stubs' . DIRECTORY_SEPARATOR . 'activity.php.stub');
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
                $io->success("Activity {$name} created successfully!");
                $io->text("File: {$filePath}");

                return self::SUCCESS;
            }

            $io->error("Failed to create activity file.");

            return self::FAILURE;
        } catch (Exception $e) {
            $io->error("An error occurred: " . $e->getMessage());

            return self::FAILURE;
        }
    }
}
