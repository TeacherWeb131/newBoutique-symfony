<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Command;
use App\Entity\CommandProduct;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // package packagist : faker
        $faker = Factory::create('fr_FR');
        // package packagist : davidbadura/faker-markdown-generator
        $faker->addProvider(new \DavidBadura\FakerMarkdownGenerator\FakerProvider($faker));
        // package packagist : liorchamla/faker-prices
        $faker->addProvider(new \Liior\Faker\Prices($faker));

        // une Category possède plusieurs Products
        $categoryTitles = [
            "High-tech" => ["Ordinateurs", "Matériel", "Audio"],
            "Geekeries" => ["Habits", "Goodies"],
        ];

        $products = [];

        // Une Category est parent d'une subCategory enfant
        foreach ($categoryTitles as $title => $subTitles) {
            $category = new Category;
            $category->setTitle($title);
            $manager->persist($category);

            foreach ($subTitles as $subTitle) {
                $subCategory = new Category();
                $subCategory->setTitle($subTitle)
                    ->setParent($category);
                $manager->persist($subCategory);

                // Un product est possédé par une subCategory (enfant d'une Category)
                for ($p = 0; $p < mt_rand(5, 15); $p++) {
                    $product = new Product();
                    $product->setTitle($faker->catchPhrase(40))
                        ->setIntroduction($faker->markdownP())
                        ->setDescription(
                            $faker->markdownP() . "\n\n" . $faker->markdownH3() . "\n\n" . $faker->markdownP() . "\n\n" . $faker->markdownP()
                        )
                        ->setPrice($faker->price(20, 500))
                        ->setPicture($faker->imageUrl(400, 400))
                        ->setFeatured($faker->boolean(20))
                        ->setCategory($subCategory);

                    $manager->persist($product);

                    $products[] = $product;
                }
            }
        }

        // Créations de commandes
        // Créer le champ createdAt (dateTime) dans l'entité Command
        for ($c = 0; $c < 10; $c++) {
            $command = new Command();
            $command->setAddress($faker->address)
                ->setCreatedAt($faker->dateTimeBetween("-6 months"));

            $manager->persist($command);

            // Faire les liens avec les Product (relation Command OneToMany Product)
            $randomProducts = $faker->randomElements($products, 4);

            foreach ($randomProducts as $product) {
                $commandProduct = new CommandProduct();
                $commandProduct->setProduct($product)
                    ->setCommand($command)
                    ->setQuantity(mt_rand(1, 3));

                // $command->addCommandProduct($commandProduct);

                $manager->persist($commandProduct);
            }
        }

        $manager->flush();
    }
}
