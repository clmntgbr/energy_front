<?php

namespace App\MessageHandler;

use App\Common\EntityId\GasStationId;
use App\Entity\Address;
use App\Entity\GasStation;
use App\Entity\GooglePlace;
use App\Lists\GasStationStatusReference;
use App\Message\CreateGasStationMessage;
use App\Message\FormatAddressMessage;
use App\Repository\GasStationRepository;
use App\Service\Uuid;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CreateGasStationMessageHandler
{
    public function __construct(
        private EntityManagerInterface $em,
        private readonly MessageBusInterface $messageBus,
        private readonly GasStationRepository $gasStationRepository
    ) {
    }

    public function __invoke(CreateGasStationMessage $message)
    {
        if (!$this->em->isOpen()) {
            $this->em = EntityManager::create($this->em->getConnection(), $this->em->getConfiguration());
        }

        $gasStation = $this->gasStationRepository->findOneBy(['id' => $message->getGasStationId()->getId()]);

        if ($gasStation instanceof GasStation) {
            throw new UnrecoverableMessageHandlingException(sprintf('Gas Station already exist (id : %s)', $message->getGasStationId()->getId()));
        }

        if ('' === $message->getLatitude() || '' === $message->getLongitude()) {
            throw new UnrecoverableMessageHandlingException(sprintf('Gas Station longitude/latitude is empty (id : %s)', $message->getGasStationId()->getId()));
        }

        $address = new Address();
        $address
            ->setCity($message->getCity())
            ->setPostalCode($message->getCp())
            ->setLongitude($message->getLongitude() ? strval(floatval($message->getLongitude()) / 100000) : null)
            ->setLatitude($message->getLatitude() ? strval(floatval($message->getLatitude()) / 100000) : null)
            ->setCountry($message->getCountry())
            ->setStreet($message->getStreet())
            ->setVicinity(sprintf('%s, %s %s, %s', $message->getStreet(), $message->getCp(), $message->getCity(), $message->getCountry()));

        $element = $message->getElement();
        unset($element['prix']);

        $gasStation = new GasStation();
        $gasStation
            ->setId($message->getGasStationId()->getId())
            ->setPop($message->getPop())
            ->setElement($element)
            ->setAddress($address)
            ->setGooglePlace(new GooglePlace())
            ->setStatus(GasStationStatusReference::CREATED);

        $filename = sprintf('%s.jpg', Uuid::v4());
        copy('public/images/75d481da-5dd4-497e-a426-f6367685c042.jpg', sprintf('public/images/gas_stations/%s', $filename));

        $gasStation->getImage()->setName($filename);
        $gasStation->getImage()->setOriginalName($filename);
        $gasStation->getImage()->setDimensions([660, 440]);
        $gasStation->getImage()->setMimeType('jpg');
        $gasStation->getImage()->setSize(86110);

        $this->isGasStationClosed($element, $gasStation);

        if (null !== $gasStation->getClosedAt()) {
            $gasStation->setStatus(GasStationStatusReference::CLOSED);
        }

        $this->em->persist($gasStation);
        $this->em->flush();

        // $this->messageBus->dispatch(
        //     new FormatAddressMessage(new GasStationId($gasStation->getId()))
        // );
    }

    /**
     * @param array<mixed> $element
     */
    public function isGasStationClosed(array $element, GasStation $gasStation): void
    {
        if (isset($element['fermeture']['attributes']['type']) && 'D' == $element['fermeture']['attributes']['type']) {
            $gasStation
                ->setClosedAt(DateTimeImmutable::createFromFormat('Y-m-d H:i:s', str_replace('T', ' ', substr($element['fermeture']['attributes']['debut'], 0, 19))));
        }
    }
}