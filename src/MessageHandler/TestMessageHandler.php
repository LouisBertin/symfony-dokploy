<?php

namespace App\MessageHandler;

use App\Scheduler\TestMessage;
use App\Entity\TestData;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class TestMessageHandler
{
    public function __construct(
        private ManagerRegistry $registry
    ) {
    }

    public function __invoke(TestMessage $message): void
    {
        try {
            // Récupérer le repository et compter les enregistrements
            $count = $this->registry->getRepository(TestData::class)->count([]);

            echo "it works ✅ - {$count} enregistrement(s) dans la base de données\n";
        } catch (\Exception $e) {
            echo "❌ Erreur lors de l'accès à la base: " . $e->getMessage() . "\n";
        }
    }
}
