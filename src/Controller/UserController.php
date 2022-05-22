<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Entity\User;
use App\Form\AppointmentType;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        $form = $this->createForm(UserType::class, $this->getUser());
        $appointments = $this->getUser()->getAppointments()->toArray();
        usort($appointments, fn($a, $b) => $b->getDate() <=> $a->getDate());

        return $this->renderForm('user/index.html.twig', [
            'form' => $form,
            'appointments' => $appointments,
        ]);
    }
}
