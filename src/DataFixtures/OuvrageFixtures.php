<?php

namespace App\DataFixtures;

use App\Entity\Ouvrage;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;
use Faker\Factory;

class OuvrageFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $editeurs = ['Gallimard', 'Flammarion', 'Hachette', 'Le Seuil', 'Actes Sud', 'Dunod', 'O\'Reilly'];
        $categories = ['Roman', 'Science', 'Histoire', 'Philosophie', 'Informatique', 'Art', 'Poésie'];
        $tagsPool = ['classique', 'nouveau', 'tech', 'science', 'roman', 'jeunesse', 'référence'];
        $langues = ['fr', 'en', 'de', 'es', 'it'];

        $total = 600;
        $batchSize = 50;

        for ($i = 0; $i < $total; $i++) {
            $ouvrage = new Ouvrage();

            $titre = $faker->sentence(mt_rand(2, 6));
            $ouvrage->setTitre($titre);

            // auteurs : 1 à 4 auteurs
            $auteurs = [];
            $countAuteurs = mt_rand(1, 4);
            for ($a = 0; $a < $countAuteurs; $a++) {
                $auteurs[] = $faker->name();
            }
            $ouvrage->setAuteurs($auteurs);

            $ouvrage->setEditeur($faker->randomElement($editeurs));

            // ISBN parfois null
            $ouvrage->setISBN(mt_rand(0, 4) ? $faker->isbn13() : null);
            $ouvrage->setISSN(mt_rand(0, 6) ? $faker->regexify('[0-9]{4}-[0-9]{3}[0-9X]') : null);

            // catégories / tags / langues (chaque champ stocké en JSON dans l'entité)
            $ouvrage->setCatégories([$faker->randomElement($categories)]);
            $ouvrage->setTags($faker->randomElements($tagsPool, mt_rand(1, 3)));
            $ouvrage->setLangues([$faker->randomElement($langues)]);

            // Année : DateTimeImmutable
            $year = (string)mt_rand(1950, (int)date('Y'));
            $ouvrage->setAnnée(new \DateTimeImmutable($year . '-01-01'));

            $ouvrage->setResume($faker->paragraphs(mt_rand(1, 4), true));

            $manager->persist($ouvrage);

            if (($i + 1) % $batchSize === 0) {
                $manager->flush();
                $manager->clear(); // libère la mémoire
            }
        }

        // flush final si nécessaire
        $manager->flush();
        $manager->clear();
    }
}