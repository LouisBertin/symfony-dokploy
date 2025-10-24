<?php

namespace App\Service;

use League\Csv\Writer;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Psr\Log\LoggerInterface;

class CsvExportService
{
    private S3Client $s3Client;
    private string $bucketName;

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly string $awsAccessKeyId,
        private readonly string $awsSecretAccessKey,
        private readonly string $awsRegion,
        private readonly string $s3BucketName,
        private readonly string $s3Endpoint
    ) {
        $s3Config = [
            'version' => 'latest',
            'region'  => $this->awsRegion,
            'credentials' => [
                'key'    => $this->awsAccessKeyId,
                'secret' => $this->awsSecretAccessKey,
            ],
        ];

        if ($this->s3Endpoint) {
            $s3Config['endpoint'] = $this->s3Endpoint;
        }

        $this->s3Client = new S3Client($s3Config);
        $this->bucketName = $this->s3BucketName;
    }

    public function exportDataToCsv(array $data, array $headers, string $filename = null): string
    {
        try {
            // Générer un nom de fichier si non fourni
            if (!$filename) {
                $filename = sprintf('export-%s.csv', date('Y-m-d-H-i-s'));
            }

            // Générer le contenu CSV
            $csvContent = $this->generateCsv($headers, $data);

            // Uploader le fichier sur S3
            $this->uploadToS3($filename, $csvContent);

            $this->logger->info('CSV export successful', [
                'filename' => $filename,
                'rows_count' => count($data)
            ]);

            return $filename;

        } catch (\Exception $e) {
            $this->logger->error('CSV export failed', [
                'filename' => $filename ?? 'unknown',
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    private function generateCsv(array $headers, array $data): string
    {
        $csv = Writer::createFromString('');

        // Ajouter les en-têtes
        if (!empty($headers)) {
            $csv->insertOne($headers);
        }

        // Ajouter les données
        foreach ($data as $row) {
            $csv->insertOne($row);
        }

        return $csv->toString();
    }

    private function uploadToS3(string $filename, string $content): void
    {
        try {
            // Gérer le bucket et le chemin pour Scaleway
            $bucketParts = explode('/', $this->bucketName, 2);
            $bucket = $bucketParts[0];
            $key = isset($bucketParts[1]) ? rtrim($bucketParts[1], '/') . '/' . $filename : $filename;

            $result = $this->s3Client->putObject([
                'Bucket' => $bucket,
                'Key'    => $key,
                'Body'   => $content,
                'ContentType' => 'text/csv',
                'Metadata' => [
                    'generated-at' => (new \DateTime())->format('c'),
                    'source' => 'symfony-job'
                ]
            ]);

            $this->logger->info('File uploaded to S3', [
                'bucket' => $bucket,
                'key' => $key,
                'url' => $result['ObjectURL'] ?? null
            ]);

        } catch (AwsException $e) {
            $this->logger->error('S3 upload failed', [
                'bucket' => $bucket,
                'key' => $key,
                'error' => $e->getAwsErrorMessage(),
                'code' => $e->getAwsErrorCode()
            ]);

            throw new \RuntimeException('Failed to upload file to S3: ' . $e->getMessage(), 0, $e);
        }
    }
}
