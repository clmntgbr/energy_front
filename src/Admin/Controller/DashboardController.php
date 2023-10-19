<?php

namespace App\Admin\Controller;

use App\Entity\Address;
use App\Entity\Currency;
use App\Entity\GasPrice;
use App\Entity\GasService;
use App\Entity\GasStation;
use App\Entity\GasStationBrand;
use App\Entity\GasType;
use App\Entity\GooglePlace;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('@EasyAdmin/page/content.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('App');
    }

    public function configureCrud(): Crud
    {
        $crud = Crud::new();

        return $crud
            ->addFormTheme('bundles/EasyAdminBundle/crud/form.html.twig')
            ->setDefaultSort(['updatedAt' => 'DESC']);
    }

    /** @param User $user */
    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)->setName($user->getEmail());
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToUrl('Api Docs', 'fas fa-map-marker-alt', '/api/docs');
        yield MenuItem::linkToCrud('GasStation', 'fas fa-list', GasStation::class);
        yield MenuItem::linkToCrud('GasService', 'fas fa-list', GasService::class);
        yield MenuItem::linkToCrud('GasStationBrand', 'fas fa-list', GasStationBrand::class);
        yield MenuItem::linkToCrud('GasPrice', 'fas fa-list', GasPrice::class);
        yield MenuItem::linkToCrud('GasType', 'fas fa-list', GasType::class);
        yield MenuItem::linkToCrud('Currency', 'fas fa-list', Currency::class);
        yield MenuItem::linkToCrud('GooglePlace', 'fas fa-list', GooglePlace::class);
        yield MenuItem::linkToCrud('Address', 'fas fa-list', Address::class);
        yield MenuItem::linkToCrud('User', 'fas fa-list', User::class);
        yield MenuItem::linktoRoute('Validation', 'fa fa-list', 'app_admin_validation');
    }
}
