<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_USER")
 */
class InventoryController extends AbstractController
{
    #[Route('/inventory', name: 'inventory')]
    public function index(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        dd($user);
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/InventoryController.php',
        ]);
    }
}
