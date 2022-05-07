<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Entity\AppointmentStatus;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin', name: 'app_admin')]
class AdminController extends AbstractController
{
    #[Route('/', name: '_dashboard')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $appointments = $entityManager->getRepository(Appointment::class)->findAll();
        $appointmentsByDate = [];
        $weekDays = 7;
        $date = new \DateTime();

        for ($i = 0; $i <= $weekDays; $i++) {
            $appointmentsByDate[$date->modify('-1 day')->format('Y-m-d')] = 0;
        }

        /** @var Appointment $appointment */
        foreach ($appointments as $appointment) {
            $date = $appointment->getDate()->format('Y-m-d');

            if (isset($appointmentsByDate[$date])) {
                $appointmentsByDate[$date]++;
            }
        }

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'appointments' => $appointments,
            'appointments_by_date' => $appointmentsByDate,
        ]);
    }

    #[Route('/appointment/confirm/{id}', name: '_appointment_confirm', requirements: ['id' => '\d+'])]
    public function confirmAppointment(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $referer = $request->headers->get('referer') ?: $this->generateUrl('app_admin_dashboard');
        /** @var Appointment $appointment */
        $appointment = $entityManager->getRepository(Appointment::class)->find($id);

        if (!$appointment) {
            $this->addFlash('danger', 'Appointment not found!');

            return $this->redirect($referer);
        }

        if (!$appointment->isUnconfirmed()) {
            $this->addFlash('danger', 'Can not confirm appointment at this stage!');

            return $this->redirect($referer);
        }

        $appointment->setStatus(
            $entityManager->getRepository(AppointmentStatus::class)
                ->find(AppointmentStatus::STATUS_CONFIRMED_ID)
        );

        $entityManager->flush();

        $this->addFlash('success', 'Successfully confirmed the appointment!');

        return $this->redirect($referer);
    }

    #[Route('/users', name: '_users')]
    public function users(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(User::class)->findAll();

        return $this->render('admin/users.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/user/make/master/{id}', name: '_make_master', requirements: ['id' => '\d+'])]
    public function makeUserMaster(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $referer = $request->headers->get('referer') ?: $this->generateUrl('app_admin_users');
        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->find($id);

        $user->setIsMaster(true);
        $entityManager->flush();

        return $this->redirect($referer);
    }
}
