<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Entity\AppointmentStatus;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MasterController extends AbstractController
{

    #[Route('/master', name: 'app_master')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser() || !$this->getUser()->isMaster()) {
            $this->addFlash('danger', 'Action not allowed!');

            return $this->redirectToRoute('app_index');
        }

        $appointments = $entityManager->getRepository(Appointment::class)->findForCurrentWeek($this->getUser());
        $weekStart = new \DateTime('monday this week');
        $weekEnd = new \DateTime('saturday this week');

        return $this->render('master/index.html.twig', [
            'appointmentsByDates' => $appointments,
            'start' => $weekStart,
            'end' => $weekEnd,
        ]);
    }
}
