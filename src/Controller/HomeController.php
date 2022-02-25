<?php

namespace App\Controller;

use App\Entity\Item;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $em->getRepository(User::class)->findOneBy(['id' => 3]);
        /** @var Item $item */
        $item = $em->getRepository(Item::class)->findOneBy(['reference' => 10332]);
        //$items = $em->getRepository(Item::class)->findAll();

        $inventory = $user->getInventory();

        $items = $em->getRepository(Item::class)->findCraftableObject($inventory);
        dd($items);
        $craftAvailable = [];
        foreach ($inventory->getInventoryItems() as $inventoryItem) {
            foreach ($items as $item) {
                $maxCraftable = 9999999;
                foreach ($item->getRecipe()->getIngredients() as $ingredient) {
                    if ($inventoryItem->getItem() === $ingredient->getItem()) {
                        $maxCraftable = $maxCraftable > $inventoryItem->getQuantity() / $ingredient->getQuantity() ? $inventoryItem->getQuantity() / $ingredient->getQuantity() : $maxCraftable;

                        $craftAvailable[$inventoryItem->getItem()->getName()] = [
                            "id" => $inventoryItem->getItem()->getId(),
                            "name" => $inventoryItem->getItem()->getName(),
                            "need" => $ingredient->getQuantity(),
                            "available" => $inventoryItem->getQuantity(),
                            "quantity" => $inventoryItem->getQuantity() / $ingredient->getQuantity(),
                        ];
                    }
                }
            }

        }
        dd($craftAvailable, $maxCraftable);
        die();

        dump('item', $item->getName());
        foreach ($item->getRecipe()->getIngredients() as $ingredient) {
            dump($ingredient->getItem()->getName() . ' - ' . $ingredient->getQuantity());
        }
        die;
        // the template path is the relative file path from `templates/`
        return $this->render('home.html.twig', [
            'page_title' => 'test',
            'user' => $user,
            'page_title' => 'test',
        ]);
    }
}
