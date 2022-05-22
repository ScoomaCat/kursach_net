<?php


namespace App\Twig;


use App\Entity\Page;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MenuExtension extends AbstractExtension
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getPages', [$this, 'getPages']),
        ];
    }

    public function getPages(): array
    {
        return $this->em->getRepository(Page::class)->findAll();
    }
}