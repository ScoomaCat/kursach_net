<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Form\AppointmentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppointmentController extends AbstractController
{
    #[Route('/appointment', name: 'app_appointment')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AppointmentType::class, new Appointment());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Appointment $appointment */
            $appointment = $form->getData();
            $appointment->setCustomer($this->getUser());

            $entityManager->persist($appointment);
            $entityManager->flush();

            $this->addFlash('success', 'Successfully created an appointment for you!');

            return $this->redirectToRoute('app_user');
        }

        return $this->renderForm('appointment/index.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/appointment/delete/{id}', name: 'app_appointment_delete', requirements: ['id' => '\d+'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $referer = $request->headers->get('referer') ?: $this->generateUrl('app_user');
        /** @var Appointment $appointment */
        $appointment = $entityManager->getRepository(Appointment::class)->find($id);

        if (!$appointment) {
            $this->addFlash('danger', 'Appointment not found!');

            return $this->redirect($referer);
        }

        if ($appointment->getCustomer() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'Action not allowed!');

            return $this->redirect($referer);
        }

        $entityManager->remove($appointment);
        $entityManager->flush();

        $this->addFlash('success', 'Successfully deleted your appointment!');

        return $this->redirect($referer);
    }
}
