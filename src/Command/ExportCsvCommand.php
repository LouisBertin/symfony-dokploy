<?php

namespace App\Command;

use App\Service\CsvExportService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:export-csv',
    description: 'Export data to CSV and upload to S3'
)]
class ExportCsvCommand extends Command
{
    public function __construct(private readonly CsvExportService $csvExportService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'custom-data',
                null,
                InputOption::VALUE_NONE,
                'Export custom sample data instead of TestEntity data'
            )
            ->setHelp('This command allows you to export data to CSV and upload it to S3');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('CSV Export to S3');

        try {
            if ($input->getOption('custom-data')) {
                // Exemple de données personnalisées
                $data = [
                    ['John Doe', 'john@example.com', 'Active', '2023-01-15'],
                    ['Jane Smith', 'jane@example.com', 'Inactive', '2023-02-20'],
                    ['Bob Wilson', 'bob@example.com', 'Active', '2023-03-10']
                ];

                $headers = ['Name', 'Email', 'Status', 'Join Date'];
                $filename = $this->csvExportService->exportDataToCsv($data, $headers, 'users-export.csv');

                $io->success("Custom data export successful! File uploaded as: {$filename}");
            } else {
                // Exemple de données simples
                $data = [
                    ['Test 1', 'Message de test 1'],
                    ['Test 2', 'Message de test 2'],
                    ['Test 3', 'Message de test 3']
                ];

                $headers = ['Name', 'Message'];
                $filename = $this->csvExportService->exportDataToCsv($data, $headers, 'test-data-export.csv');

                $io->success("Test data export successful! File uploaded as: {$filename}");
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $io->error('Export failed: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
