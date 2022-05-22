<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Entity\AppointmentStatus;
use App\Entity\Page;
use App\Entity\User;
use App\Form\AppointmentType;
use App\Form\PageType;
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

    #[Route('/pages', name: '_pages')]
    public function pages(EntityManagerInterface $entityManager): Response
    {
        $pages = $entityManager->getRepository(Page::class)->findAll();

        return $this->render('admin/pages.html.twig', [
            'pages' => $pages,
        ]);
    }

    #[Route('/pages/edit', name: '_page_edit')]
    public function pageEdit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $id = $request->get('id') ?: 0;
        $page = $entityManager->getRepository(Page::class)->find($id);

        if (!$page) {
            $page = new Page();
        }

        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $page = $form->getData();
            $entityManager->persist($page);
            $entityManager->flush();

            $this->addFlash('success', 'Success!');

            return $this->redirectToRoute('app_admin_pages');
        }

        return $this->render('admin/page_edit.html.twig', [
            'form' => $form->createView(),
            'page' => $page,
        ]);
    }


    #[Route('/pages/delete/{id}', name: '_delete_page', requirements: ['id' => '\d+'])]
    public function deletePage(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $referer = $request->headers->get('referer') ?: $this->generateUrl('app_admin_users');
        /** @var Page $page */
        $page = $entityManager->getRepository(Page::class)->find($id);

        $entityManager->remove($page);
        $entityManager->flush();

        return $this->redirect($referer);
    }
}
