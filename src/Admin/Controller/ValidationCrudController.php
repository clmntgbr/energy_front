<?php

namespace App\Admin\Controller;

use App\Entity\EntityId\GasStationId;
use App\Entity\GasStation;
use App\Lists\GasStationStatusReference;
use App\Message\CreateGooglePlaceDetailsMessage;
use App\Message\CreateGooglePlaceTextsearchMessage;
use App\Repository\GasStationRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class ValidationCrudController extends AbstractController
{
    #[Route('/admin/validation', name: 'app_admin_validation')]
    public function index(AdminUrlGenerator $adminUrlGenerator, GasStationRepository $gasStationRepository, Request $request): Response
    {
        $gasStationId = $request->query->get('gasStationId') ?? null;

        if (null !== $gasStationId) {
            $gasStation = $gasStationRepository->findOneBy(['gasStationId' => $gasStationId]);
        }

        if (null === $gasStationId) {
            $gasStation = $gasStationRepository->findRandomGasStation();
        }

        $gasStations = $gasStationRepository->getGasStationGooglePlaceByPlaceId($gasStation);

        $url = $adminUrlGenerator
            ->setController(GasStationCrudController::class)
            ->setAction(Action::EDIT)
            ->setEntityId($gasStation?->getId())
            ->generateUrl();

        return $this->render('Admin/validation.html.twig', [
            'gasStation' => $gasStation,
            'gasStations' => $gasStations,
            'gasStationUrlEdit' => $url,
        ]);
    }

    #[Route('/admin/validation/validate/{gasStationId}', name: 'app_admin_validation_validate')]
    public function validate(EntityManagerInterface $em, GasStation $gasStation): Response
    {
        if (GasStationStatusReference::WAITING_VALIDATION !== $gasStation->getStatus()) {
            return $this->redirect('/admin?routeName=app_admin_validation');
        }

        $gasStation->setStatus(GasStationStatusReference::VALIDATED);
        $gasStation->setStatus(GasStationStatusReference::OPEN);

        $em->persist($gasStation);
        $em->flush();

        return $this->redirect('/admin?routeName=app_admin_validation');
    }

    #[Route('/admin/validation/rejected/textsearch/{gasStationId}', name: 'app_admin_validation_rejected_textsearch')]
    public function rejectedToTextSearch(EntityManagerInterface $em, MessageBusInterface $messageBus, GasStation $gasStation): Response
    {
        if (GasStationStatusReference::WAITING_VALIDATION !== $gasStation->getStatus()) {
            return $this->redirect('/admin?routeName=app_admin_validation');
        }

        $gasStation->setStatus(GasStationStatusReference::VALIDATION_REJECTED);

        $em->persist($gasStation);
        $em->flush();

        $messageBus->dispatch(
            new CreateGooglePlaceTextsearchMessage(new GasStationId($gasStation->getGasStationId()))
        );

        return $this->redirect('/admin?routeName=app_admin_validation');
    }

    #[Route('/admin/validation/rejected/placedetails/{gasStationId}', name: 'app_admin_validation_rejected_placedetails')]
    public function rejectedToPlaceDetails(EntityManagerInterface $em, MessageBusInterface $messageBus, GasStation $gasStation): Response
    {
        if (GasStationStatusReference::WAITING_VALIDATION !== $gasStation->getStatus()) {
            return $this->redirect('/admin?routeName=app_admin_validation');
        }

        $gasStation->setStatus(GasStationStatusReference::VALIDATION_REJECTED);

        $em->persist($gasStation);
        $em->flush();

        $messageBus->dispatch(
            new CreateGooglePlaceDetailsMessage(new GasStationId($gasStation->getGasStationId()))
        );

        return $this->redirect('/admin?routeName=app_admin_validation');
    }
}
