<?php

namespace App\Controller;

use App\Entity\TestData;
use App\Form\TestDataForm;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DataController extends AbstractController
{
    public function __construct(
        private ManagerRegistry $registry
    ) {
    }

    #[Route('/data', name: 'app_data')]
    public function index(Request $request): Response
    {
        // Récupérer les données existantes
        $data = $this->registry->getRepository(TestData::class)->findBy([], ['createdAt' => 'DESC']);

        // Créer le formulaire d'ajout
        $testData = new TestData();
        $form = $this->createForm(TestDataForm::class, $testData);
        $form->handleRequest($request);

        // Traiter le formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->registry->getManager();
            $entityManager->persist($testData);
            $entityManager->flush();

            $this->addFlash('success', 'Donnée ajoutée avec succès !');
            return $this->redirectToRoute('app_data');
        }

        return $this->render('data/index.html.twig', [
            'testData' => $data,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/data/delete/{id}', name: 'app_data_delete')]
    public function delete(int $id): Response
    {
        $testData = $this->registry->getRepository(TestData::class)->find($id);

        if (!$testData) {
            $this->addFlash('error', 'Donnée non trouvée');
            return $this->redirectToRoute('app_data');
        }

        $entityManager = $this->registry->getManager();
        $entityManager->remove($testData);
        $entityManager->flush();

        $this->addFlash('success', 'Donnée supprimée avec succès !');
        return $this->redirectToRoute('app_data');
    }
}
