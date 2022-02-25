<?php

namespace App\Command;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Entity\Item;
use App\Entity\Job;
use App\Entity\Type;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(
    name: 'app:import:data',
    description: 'Add a short description for your command',
)]
class ImportDataCommand extends Command
{
    public ParameterBagInterface $parameterBag;
    public EntityManagerInterface $em;

    public function __construct(ParameterBagInterface $parameterBag, EntityManagerInterface $em)
    {
        parent::__construct();
        $this->parameterBag = $parameterBag;
        $this->em = $em;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        /** @var Item $item */
        $item = $this->em->getRepository(Item::class)->findOneBy(["reference" => 10332]);
        if ($item) {
            foreach ($item->getRecipe()->getIngredients() as $ingredient) {
                dump($ingredient->getItem()->getName() . ' ' . $ingredient->getQuantity());
            }
            die;
        }

        $dataJson = json_decode(file_get_contents($this->parameterBag->get('kernel.project_dir') . '/public/Data.json'), true);
        $jobsJson = json_decode(file_get_contents($this->parameterBag->get('kernel.project_dir') . '/public/jobs.json'), true);
        $rarities = json_decode(file_get_contents($this->parameterBag->get('kernel.project_dir') . '/public/rarity.json'), true);
        $types = json_decode(file_get_contents($this->parameterBag->get('kernel.project_dir') . '/public/types.json'), true);

        $this->importJob($jobsJson);
        $this->importTypes($types);
        $this->importData($dataJson, $rarities);

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }

    /**
     * @param array $jobsJson
     * @return void
     */
    private function importJob(array $jobsJson): void
    {
        foreach ($jobsJson as $jobJson) {
            $name = $jobJson['name'][0];
            $id = $jobJson['id'];
            $job = $this->em->getRepository(Job::class)->findOneBy(["reference" => $id]);
            if (!$job) {
                $job = new Job();
            }

            $job->setName($name)->setReference($id);
            $this->em->persist($job);
        }
        $this->em->flush();
    }

    /**
     * @param array $typesJson
     * @return void
     */
    private function importTypes(array $typesJson): void
    {
        foreach ($typesJson as $typeJson) {
            $name = $typeJson['name'][0];
            $id = $typeJson['id'];
            $type = $this->em->getRepository(Type::class)->findOneBy(["reference" => $id]);
            if (!$type) {
                $type = new Type();
            }

            $type->setName($name)->setReference($id);
            $this->em->persist($type);
        }
        $this->em->flush();
    }

    /**
     * @param array $dataJson
     * @param array $raritiesJson
     * @return void
     */
    private function importData(array $dataJson, array $raritiesJson)
    {
        $rarities = $this->getRarity($raritiesJson);
        foreach ($dataJson as $data) {
//          if (!$data['id'] || !($data['id'] == 10332 || $data['id'] == 17374) ) {
            if (!$data['id']) {
                continue;
            }

            $name = $data['name'];
            $reference = $data['id'];
            $image = $data['img'];
            $lvlItem = $data['lvlItem'];
            $rarity = $rarities[$data['rarity']];
            $ingredients = $data['ingredients'][0] ?? null;
            $lvlCraft = $data['lvl'];
            $upgrade = $data['upgrade'];
            $jobId = $data['job'];
            $typeId = $data['type'];
            $nb = $data['nb'];

            $job = $this->em->getRepository(Job::class)->findOneBy(["reference" => $jobId]);
            if (!$job) {
                dd('Impossible de trouver job ' . $jobId);
            }

            $type = $this->em->getRepository(Type::class)->findOneBy(["reference" => $typeId]);
            if (!$type) {
                dd('Impossible de trouver le type ' . $jobId);
            }

            $item = $this->em->getRepository(Item::class)->findOneBy(["reference" => $reference]);

            if (!$item) {
                $item = new Item();
            }

            $item->setReference($reference);
            $item->setImage($image);
            $item->setName($name[0]);
            $item->setNb($nb);
            $item->setLvlCraft($lvlCraft);
            $item->setLvlItem($lvlItem);
            $item->setUpgrade($upgrade);
            $item->setJob($job);
            $item->setType($type);
            $item->setRarity($rarity);
            $this->setIngredients($item, $ingredients);
            $this->em->persist($item);
        }
        $this->em->flush();
    }

    /**
     * @param array $raritiesJson
     * @return array
     */
    private function getRarity(array $raritiesJson): array
    {
        $rarityObject = [];

        foreach ($raritiesJson as $rarityJson) {
            $name = $rarityJson['name'][0];
            $id = $rarityJson['id'];

            $rarityObject[$id] = $name;
        }
        return $rarityObject;
    }

    /**
     * @param Item $item
     * @param array $ingredient
     * @return void
     */
    private function setIngredients(Item $item, ?array $ingredients)
    {
        if (!$ingredients) {
            return;
        }

        foreach ($ingredients as $ingredient) {

            $qty = $ingredient['qt'];
            /** @var Item $itemIngredient */
            $itemIngredient = $this->em->getRepository(Item::class)->findOneBy(["reference" => $ingredient['id']]);

            if (!$itemIngredient) {
                continue;
            }
            $recipe = $this->em->getRepository(Recipe::class)->findOneBy(['id' => $item->getRecipe()]);

            if (!$recipe) {
                $recipe = new Recipe();
                $recipe->setItem($item);
            }

            $ingredientObject = $this->em->getRepository(Ingredient::class)->findOneBy(['item' => $itemIngredient, 'quantity' => $qty, 'recipe' => $recipe]);

            if (!$ingredientObject) {
                $ingredientObject = new Ingredient();
            }

            $ingredientObject->setQuantity($qty)->setItem($itemIngredient);
            $ingredientObject->setRecipe($recipe);
            $recipe->addIngredient($ingredientObject);
            $this->em->persist($ingredientObject);
            $this->em->persist($recipe);
        }
        $this->em->persist($item);
        $this->em->flush();
    }
}
