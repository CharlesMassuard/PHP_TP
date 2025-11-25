<?php
namespace App\DataFixtures;

use App\Entity\Exemplaires;
use App\Entity\Ouvrage;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class ExemplairesFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $ouvrages = $manager->getRepository(Ouvrage::class)->findAll();

        foreach ($ouvrages as $ouvrage) {
            for ($i = 0; $i < mt_rand(1, 5); $i++) {
                $exemplaire = new Exemplaires();
                $exemplaire->setCote($faker->regexify('[A-Z]{1}-[0-9]{3}-[0-9]{3}'));
                $exemplaire->setEtat($faker->randomElement(['bon', 'moyen', 'mauvais']));
                $exemplaire->setEmplacement($faker->sentence(3));
                $exemplaire->setDisponibilite($faker->boolean());
                $exemplaire->setOuvrage($ouvrage);

                $manager->persist($exemplaire);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            OuvrageFixtures::class,
        ];
    }
}