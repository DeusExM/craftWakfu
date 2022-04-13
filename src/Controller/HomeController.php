<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Entity\InventoryItems;
use App\Entity\Item;
use App\Entity\ItemToCraft;
use App\Entity\Recipe;
use App\Entity\Sale;
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

    public function __construct(private EntityManagerInterface $em)
    {
    }

    #[Route('/', name: 'home')]
    public function index(): Response
    {

        /** @var User $user */
        $user = $this->getUser();
        $itemCraftable = [];
        $itemNonCraftable = [];
        $inventory = $user->getInventory();

        $inventoryItems = [];

        if (!$inventory) {

            return $this->render('home.html.twig', [
                'page_title' => 'test',
                'user' => null,
                'inventoryItems' => null,
                'itemCraftable' => null,
                'itemNonCraftable' => null,
            ]);
        }
        /** @var InventoryItems $inventoryItem */
        foreach ($inventory?->getInventoryItems() as $inventoryItem) {
            $inventoryItems[$inventoryItem->getItem()?->getName()] = ["item" => $inventoryItem->getItem(), "qty" => $inventoryItem->getQuantity()];
        }
        $itemsCouldBeCraft = $this->em->getRepository(Item::class)->findCraftableItems($inventoryItems);

        $itemCraftable = [];
        $itemNonCraftable = [];
        /** @var Item $item */
        foreach ($itemsCouldBeCraft as $item) {
            $item->totalPrice = 0;
            $maxItemCraftable = 999999;
            $canDoThisItem = true;
            $numberIngredientAvailable = ['count_items_needed' => $item->getRecipe()?->getIngredients()->count(), 'count_items_available' => 0];
            /** @var Ingredient $ingredient */
            foreach ($item->getRecipe()?->getIngredients() as $ingredient) {
                $item->totalPrice += $ingredient->getItem()?->getAveragePrice() * $ingredient->getQuantity();

                if (isset($inventoryItems[$ingredient->getItem()?->getName()])) {

                    $ingredient->getItem()->haveSome = true;
                    $ingredient->getItem()->qty = $inventoryItems[$ingredient->getItem()?->getName()]['qty'] ?? 0;

                    if (($ingredient->getItem()->qty - $ingredient->getQuantity()) >= 0) {
                        $numberIngredientAvailable['count_items_available']++;
                    }

                    if ($ingredient->getItem()->qty) {
                        if (($ingredient->getItem()->qty / $ingredient->getQuantity()) < $maxItemCraftable) {
                            $maxItemCraftable = $ingredient->getItem()->qty / $ingredient->getQuantity();
                        }
                    }


                }

                if (!isset($inventoryItems[$ingredient->getItem()?->getName()]['qty']) || ($inventoryItems[$ingredient->getItem()?->getName()]['qty'] - $ingredient->getQuantity()) <= 0) {
                    $canDoThisItem = false;
                }
            }

            $item->numberIngredientAvailable = $numberIngredientAvailable;
            $item->maxItemCraftable = $maxItemCraftable === 999999 || !$canDoThisItem ? 0 : (int)$maxItemCraftable;
            $maxMissingIngredient = 2;
            if ($numberIngredientAvailable['count_items_needed'] - $numberIngredientAvailable['count_items_available'] <= $maxMissingIngredient) {
                if ($canDoThisItem) {
                    $itemCraftable[] = $item;
                } else {
                    $itemNonCraftable[] = $item;
                }
            }
        }

        return $this->render('home.html.twig', [
            'page_title' => 'test',
            'user' => $user,
            'inventoryItems' => $inventoryItems,
            'itemCraftable' => $itemCraftable,
            'itemNonCraftable' => $itemNonCraftable,
        ]);
    }

    /**
     * @throws \Exception
     */
    #[Route('/ventes', name: 'sales')]
    public function sales(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $itemsInfo = [];

        $totalAllCost = 0;
        $totalAllSold = 0;
        $totalAllUnsold = 0;

        /** @var ItemToCraft $itemToCraft */
        foreach ($user->getItemToCrafts() as $itemToCraft) {
            $itemToCraft->craft = $this->getFullRecipe($itemToCraft->getItem());
            $itemToCraft->totalCost = 0;
            $itemToCraft->cost = 0;
            $itemToCraft->totalSale = 0;
            $itemToCraft->totalToSale = 0;
            $itemToCraft->totalExtraCost = 0;
            $itemToCraft->totalMargin = 0;
            $itemToCraft->totalTaxes = 0;
            $itemToCraft->averageNumberOfDays = 0;
            $itemToCraft->averageNumberOfDays = 0;
            foreach ($itemToCraft->craft as $cr) {
                $itemToCraft->totalCost += $itemToCraft->getItem()->getsales() ? $cr['total'] * count($itemToCraft->getItem()->getsales()) : 0;
                $itemToCraft->cost += $cr['total'];
            }

            $saleDone = 0;
            //Add taxes to total cost and add sales
            /** @var Sale $sale */
            foreach ($itemToCraft->getItem()->getsales() as $sale) {
                $itemToCraft->totalTaxes  += $sale->getTax();
                $itemToCraft->totalCost += $sale->getExtraCost();
                $itemToCraft->totalExtraCost += $sale->getExtraCost();
                $sale->timeToSale = '-';
                if ($sale->getSaleAt() && $sale->getForSaleAt()) {
                    $saleDone++;
                    $sale->timeToSale = $sale->getSaleAt()->diff($sale->getForSaleAt());
                    $sale->timeToSale = $sale->timeToSale->format('%a');
                    $itemToCraft->averageNumberOfDays += $sale->timeToSale;
                }

                if ($sale->getSaleAt()) {
                    $itemToCraft->totalSale += $sale->getSale();
                    $totalAllSold += $sale->getSale();
                } else {
                    $itemToCraft->totalToSale += $sale->getSale();
                    $totalAllUnsold += $sale->getSale();
                }
            }

            $itemToCraft->totalCost += $itemToCraft->totalTaxes ;
            $itemToCraft->totalMargin = $itemToCraft->totalSale - $itemToCraft->totalCost;
            $itemToCraft->averageNumberOfDays = $saleDone !== 0 ? $itemToCraft->averageNumberOfDays / $saleDone : 0;

            $totalAllCost += $itemToCraft->totalCost;
        }
        $totalAllMargin = $totalAllSold - $totalAllCost;

        return $this->render('sales.html.twig', [
            'page_title' => 'Ventes',
            'sales' => $user->getSales(),
            'itemsToCraft' => $user->getItemToCrafts(),
            'totalAllCost' => $totalAllCost,
            'totalAllSold' => $totalAllSold,
            'totalAllUnsold' => $totalAllUnsold,
            'totalAllMargin' => $totalAllMargin,
            'totalAllcoef' => $totalAllMargin / $totalAllCost,
            'totalAllcoefIfSold' => ($totalAllSold + $totalAllUnsold - $totalAllCost) / $totalAllCost,
        ]);
    }

    public function getFullRecipe(Item $item)
    {
        $itemSameNames = $this->em->getRepository(Item::class)->findItemSameName($item);
        $recipesId = [];
        /** @var Item $item */
        foreach ($itemSameNames as $itemSameName) {
            if ($itemSameName->getRecipe()->getId()) {
                $recipesId[] = $itemSameName->getRecipe()->getId();
            }
        }
        $ingredients = $this->em->getRepository(Ingredient::class)->findAllItemName($recipesId);

        $sumIngredients = [];

        /** @var Ingredient $ingredient */
        foreach ($ingredients as $ingredient) {
            if ($ingredient->getItem()?->getName() === $item->getName()) {
                continue;
            }

            if (!isset($sumIngredients[$ingredient->getItem()->getName()])) {
                $sumIngredients[$ingredient->getItem()->getName()] = [
                    "id" => $ingredient->getItem()->getId(),
                    "name" => $ingredient->getItem()->getName(),
                    "level" => $ingredient->getItem()->getLvlItem(),
                    "quantity" => $ingredient->getQuantity(),
                    "averagePrice" => $ingredient->getItem()->getAveragePrice(),
                    "total" => $ingredient->getItem()->getAveragePrice(),
                ];
            } else {
                $sumIngredients[$ingredient->getItem()->getName()]["quantity"] += + $ingredient->getQuantity();
                $sumIngredients[$ingredient->getItem()->getName()]["total"] += $ingredient->getQuantity() * $ingredient->getItem()->getAveragePrice();
            }
        }

        ksort($sumIngredients);
        return $sumIngredients;
    }
}
