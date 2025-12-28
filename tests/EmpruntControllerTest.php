<?php

namespace App\Tests\Controller;

use App\Entity\Ouvrage;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;
use App\Entity\Exemplaires;
use App\Entity\EtatExemplaire;

class EmpruntControllerTest extends WebTestCase
{
    public function testReserveExemplaire(): void
    {
        $client = static::createClient();
        [$user, $emprunt, $ouvrage, $exemplaire] = $this->createBD($client);
        $client->loginUser($user);

        $client->request('GET', '/ouvrage/' . $ouvrage->getId() . '/exemplaires/' . $exemplaire->getId() . '/reserve');

        $this->assertResponseRedirects('/ouvrage/' . $ouvrage->getId() . '/exemplaires');
        $client->followRedirect();

        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        $emprunt = $entityManager->getRepository(\App\Entity\Emprunt::class)->findOneBy([
            'user' => $user,
            'exemplaire' => $exemplaire,
            'statut' => 'Réservé',
        ]);
        $this->assertNotNull($emprunt, 'L\'emprunt n\'a pas été créé dans la base de données.');
    }

    public function testCancelReservation(): void
    {
        $client = static::createClient();
        [$user, $emprunt] = $this->createBD($client);
        $client->loginUser($user);

        $client->request('GET', '/user/reservations/' . $emprunt->getId() . '/annuler');

        $this->assertResponseRedirects('/user/reservations');
        $client->followRedirect();

        //test si l'emprunt a été mit à jour dans la BDD
        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        $emprunt = $entityManager->getRepository(\App\Entity\Emprunt::class)->find($emprunt->getId());
        $this->assertEquals('Annulé', $emprunt->getStatut(), 'Le statut de l\'emprunt n\'a pas été mis à jour dans la base de données.');
    }

    public function testReturnReservation(): void
    {
        $client = static::createClient();
        [$user, $emprunt] = $this->createBD($client, "Emprunté");
        $client->loginUser($user);

        $client->request('GET', '/user/reservations/' . $emprunt->getId() . '/retourner');

        $this->assertResponseRedirects('/user/reservations');
        $client->followRedirect();

        //test si l'emprunt a été mit à jour dans la BDD
        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        $emprunt = $entityManager->getRepository(\App\Entity\Emprunt::class)->find($emprunt->getId());
        $this->assertEquals('Retourné', $emprunt->getStatut(), 'Le statut de l\'emprunt n\'a pas été mis à jour dans la base de données.');
    }

    private function createBD($client, $statut = "Réservé"): array
    {
        $entityManager = $client->getContainer()->get('doctrine')->getManager();

        $user = new User();
        $user->setEmail('test+' . uniqid() . '@example.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword(
            $client->getContainer()->get('security.password_hasher')->hashPassword($user, 'password')
        );
        $entityManager->persist($user);
        $entityManager->flush();

        $ouvrage = new Ouvrage();
        $ouvrage->setTitre('Ouvrage de test');
        $ouvrage->setISBN('123-456-789');
        $ouvrage->setEditeur('Éditeur de test');
        $ouvrage->setAuteursFromString("Auteur Test");
        $ouvrage->setLanguesFromString("Français");
        $ouvrage->setCategoriesFromString("Catégorie Test");
        $ouvrage->setTagsFromString("Tag1, Tag2");
        $ouvrage->setAnnee(new \DateTimeImmutable('2020-01-01'));
        $ouvrage->setResume('Ceci est un résumé de test.');
        $entityManager->persist($ouvrage);
        $entityManager->flush();

        $exemplaire = new Exemplaires();
        $exemplaire->setOuvrage($ouvrage);
        $exemplaire->setCote("COTE-TEST-001");
        $exemplaire->setDisponibilite(true);
        $exemplaire->setEtat(EtatExemplaire::BON);
        $exemplaire->setEmplacement("Rayon Test");
        $entityManager->persist($exemplaire);
        $entityManager->flush();

        $emprunt = new \App\Entity\Emprunt();
        $emprunt->setUser($user);
        $emprunt->setExemplaire($exemplaire);
        $emprunt->setStatut($statut);
        $emprunt->setDateRetour(new \DateTimeImmutable('+14 days'));
        $emprunt->setDateEmprunt(new \DateTimeImmutable());
        $entityManager->persist($emprunt);
        $entityManager->flush();

        return [$user, $emprunt, $ouvrage, $exemplaire];
    }
}