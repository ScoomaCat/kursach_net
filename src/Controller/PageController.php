<?php

namespace App\Controller;

use App\Entity\Page;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    #[Route('/page/{slug}', name: 'app_page')]
    public function index(EntityManagerInterface $entityManager, string $slug): Response
    {
        $page = $entityManager->getRepository(Page::class)->findOneByUrl($slug);

        if (!$page) {
            $this->addFlash('danger', 'Such page doesnt exist!');
        }

        return $this->render('page/index.html.twig', [
            'page' => $page,
        ]);
    }
}
