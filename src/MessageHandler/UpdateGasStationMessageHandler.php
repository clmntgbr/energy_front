<?php

namespace App\MessageHandler;

use App\Entity\GasStation;
use App\Message\UpdateGasStationMessage;
use App\Repository\GasStationRepository;
use App\Repository\UserRepository;
use App\Service\GasServiceService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final class UpdateGasStationMessageHandler
{
    public function __construct(
        private EntityManagerInterface $em,
        private readonly MessageBusInterface $messageBus,
        private readonly GasStationRepository $gasStationRepository,
        private readonly UserRepository $userRepository,
        private readonly GasServiceService $gasServiceService,
    ) {
    }

    public function __invoke(UpdateGasStationMessage $message)
    {
        if (!$this->em->isOpen()) {
            $this->em->refresh();
        }

        $gasStation = $this->gasStationRepository->findOneBy(['gasStationId' => $message->getGasStationId()->getId()]);

        if (!$gasStation instanceof GasStation) {
            throw new UnrecoverableMessageHandlingException(sprintf('Gas Station doesn\'t exist (gasStationId : %s)', $message->getGasStationId()->getId()));
        }

        $element = $message->getElement();

        $gasStation
            ->setHash($message->getHash());

        $this->gasServiceService->deleteGasServices($gasStation);
        $this->gasServiceService->createGasServices($gasStation, $element);

        $this->em->persist($gasStation);
        $this->em->flush();
    }
}
