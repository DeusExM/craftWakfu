<?php
namespace App\Controller;

use App\Entity\InventoryItems;
use App\Entity\Item;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_USER")
 */
class HomeController extends AbstractController
{

    #[Route('/', name: 'home')]
    public function index(EntityManagerInterface $em): Response
    {

        /** @var User $user */
        $user = $this->getUser();

        $inventory = $user->getInventory();

        $inventoryItems = [];
        /** @var InventoryItems $inventoryItem */
        foreach ($inventory->getInventoryItems() as $inventoryItem) {
            $inventoryItems[$inventoryItem->getItem()->getName()] = ["item" => $inventoryItem->getItem(),"qty" =>  $inventoryItem->getQuantity()];
        }
        $craftableItems = $em->getRepository(Item::class)->findCraftableItems($inventoryItems);

        foreach ($craftableItems as $item) {
            foreach ($item->getRecipe()->getIngredients() as $ingredient) {
                if (isset($inventoryItems[$ingredient->getItem()->getName()])) {
                    $ingredient->getItem()->haveSome = true;
                    $ingredient->getItem()->qty = $inventoryItems[$ingredient->getItem()->getName()]['qty'];
                }
            }
        }

        /*
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
        die;*/
        // the template path is the relative file path from `templates/`

        return $this->render('home.html.twig', [
            'page_title' => 'test',
            'user' => $user,
            'inventoryItems' => $inventoryItems,
            'craftableItems' => $craftableItems,
        ]);
    }
}
