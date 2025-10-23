<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HelloController extends AbstractController
{
    #[Route('/', name: 'app_hello')]
    public function index(): Response
    {
        return $this->render('hello/index.html.twig', [
            'message' => 'Hello World',
        ]);
    }

    #[Route('/hello/{name}', name: 'app_hello_name')]
    public function helloName(string $name): Response
    {
        return $this->render('hello/name.html.twig', [
            'name' => $name,
        ]);
    }
}