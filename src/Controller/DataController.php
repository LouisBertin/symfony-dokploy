<?php

namespace App\Controller;

use App\Entity\TestData;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DataController extends AbstractController
{
    public function __construct(
        private ManagerRegistry $registry
    ) {
    }

    #[Route('/data', name: 'app_data')]
    public function index(): Response
    {
        $data = $this->registry->getRepository(TestData::class)->findBy([], ['createdAt' => 'DESC']);

        return $this->render('data/index.html.twig', [
            'testData' => $data,
        ]);
    }

    #[Route('/data/add', name: 'app_data_add')]
    public function add(): Response
    {
        return $this->render('data/add.html.twig');
    }
}
