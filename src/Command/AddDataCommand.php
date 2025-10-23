<?php

namespace App\Command;

use App\Entity\TestData;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:add-data',
    description: 'Ajoute des données de test dans la base de données',
)]
class AddDataCommand extends Command
{
    public function __construct(
        private ManagerRegistry $registry
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getOption('name');
        $message = $input->getOption('message');

        if (!$name || !$message) {
            $output->writeln('<error>Vous devez spécifier --name et --message</error>');
            return Command::FAILURE;
        }

        // Création de l'entité
        $testData = new TestData();
        $testData->setName($name);
        $testData->setMessage($message);

        // Sauvegarde en base de données
        $entityManager = $this->registry->getManager();
        $entityManager->persist($testData);
        $entityManager->flush();

        $output->writeln('✅ Données insérées avec succès !');
        $output->writeln(sprintf('📝 Nom : %s', $name));
        $output->writeln(sprintf('💬 Message : %s', $message));
        $output->writeln(sprintf('🆔 ID : %d', $testData->getId()));
        $output->writeln('🔗 Voir les données sur : /data');

        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'Le nom à insérer')
            ->addOption('message', null, InputOption::VALUE_REQUIRED, 'Le message à insérer')
            ->setHelp('Utilisez --name="<nom>" --message="<message>" pour ajouter des données');
    }
}