<?php

namespace App\DataFixtures;

use App\Entity\Exemplaires;
use App\Entity\Ouvrage;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ExemplairesFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        /** @var Ouvrage[] $ouvrages */
        $ouvrages = $manager->getRepository(Ouvrage::class)->findAll();

        $etatChoices = ['Neuf', 'Bon état', 'Usé', 'Endommagé', 'Réparé'];
        $emplacements = ['Rayon A1', 'Rayon A2', 'Rayon B3', 'Reserve', 'Prêt', 'Lecture sur place'];

        $batchSize = 100;
        $i = 0;

        foreach ($ouvrages as $ouvrage) {
            // pour chaque ouvrage, créer 1 à 5 exemplaires
            $count = mt_rand(1, 5);
            for ($j = 0; $j < $count; $j++) {
                $ex = new Exemplaires();
                // Cote : ex. "COTE-1234" ou "A-12-34"
                $ex->setCote($faker->bothify('COTE-##??'));
                $ex->setEtat($faker->randomElement($etatChoices));
                $ex->setEmplacement($faker->randomElement($emplacements));
                $ex->setDisponibilite((bool) mt_rand(0, 1));
                $ex->setOuvrage($ouvrage);

                $manager->persist($ex);

                $i++;
                if ($i % $batchSize === 0) {
                    $manager->flush();
                }
            }
        }

        $manager->flush();
    }
}