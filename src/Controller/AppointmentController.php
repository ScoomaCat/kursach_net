<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Form\AppointmentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppointmentController extends AbstractController
{
    #[Route('/appointment', name: 'app_appointment')]
    public function index(): Response
    {
        $form = $this->createForm(AppointmentType::class, new Appointment());

        return $this->renderForm('appointment/index.html.twig', [
            'form' => $form,
        ]);
    }
}
