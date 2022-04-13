<?php

namespace App\Controller\Admin;

use App\Entity\Inventory;
use App\Entity\InventoryItems;
use App\Entity\Item;
use App\Entity\ItemToCraft;
use App\Entity\Sale;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private AdminUrlGenerator $adminUrlGenerator
    ) {}

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // Option 1. Make your dashboard redirect to the same page for all users
        return $this->redirect($this->adminUrlGenerator->setController(InventoryCrudController::class)->generateUrl());

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::linkToDashboard('Dashboard', 'fa fa-home'),

            MenuItem::section('Users'),
            MenuItem::linkToCrud('Users', 'fa fa-user', User::class),

            MenuItem::section('Inventory'),
            MenuItem::linkToCrud('Inventory', 'fa fa-tags', Inventory::class),
            MenuItem::linkToCrud('InventoryItem', 'fa fa-file-text', InventoryItems::class),

            MenuItem::section('Items'),
            MenuItem::linkToCrud('Items', 'fa fa-tags', Item::class),

            MenuItem::section('Sales'),
            MenuItem::linkToCrud('Sales', 'fa fa-tags', Sale::class),

            MenuItem::section('Item To Craft'),
            MenuItem::linkToCrud('Item To Craft', 'fa fa-tags', ItemToCraft::class),

            MenuItem::section('Action'),
            MenuItem::linkToLogout('Logout', 'fa fa-exit'),
            MenuItem::linkToExitImpersonation('Stop impersonation', 'fa fa-exit'),

            MenuItem::section('Home'),
            MenuItem::linkToRoute('Home', 'fa fa-home', 'home'),
        ];
    }
}
