<?php

declare(strict_types=1);

namespace Academy\Commands;

use Academy\Services\CertificateService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

class VerifyCertificateCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('academy:verify-cert')
            ->setDescription('Verify an Academy certificate by its unique number.')
            ->addArgument('number', InputArgument::REQUIRED, 'Certificate number');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $number = $input->getArgument('number');

        $io->title('Verifying Certificate');

        try {
            $service = resolve(CertificateService::class);
            $certificate = $service->verify($number);

            if ($certificate) {
                $io->success("Certificate Verified!");
                $io->listing([
                    "Learner ID: {$certificate->enrolment->user_id}",
                    "Program: {$certificate->enrolment->program->title}",
                    "Issued At: {$certificate->issued_at}",
                ]);
            } else {
                $io->warning("Certificate Not Found or Invalid.");
            }

            return Command::SUCCESS;
        } catch (Throwable $e) {
            $io->error("Verification failed: " . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
