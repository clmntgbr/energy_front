<?php

namespace App\Controller;

use App\Repository\GasStationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(GasStationRepository $gasStationRepository, EntityManagerInterface $em): Response
    {
        return $this->render('Home/index.html.twig', []);
    }
}
