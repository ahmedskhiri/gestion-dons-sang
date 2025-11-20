<?php
// src/DataFixtures/AppFixtures.php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Stock;
use App\Entity\Lieu;
use App\Entity\Collecte;
use DateTime; 

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // --- 1. Création des Lieux (Maintenant 5) ---
        
        // LIEU 1
        $lieu1 = new Lieu();
        $lieu1->setNomLieu("ISET Sousse");
        $lieu1->setAdresse("Rue Lamine Abassi");
        $lieu1->setVille("Sousse");
        $lieu1->setCodePostal("4013");
        $manager->persist($lieu1);

        // LIEU 2
        $lieu2 = new Lieu();
        $lieu2->setNomLieu("Faculté des Sciences");
        $lieu2->setAdresse("Avenue de l'Environnement");
        $lieu2->setVille("Monastir");
        $lieu2->setCodePostal("5000");
        $manager->persist($lieu2);

        // --- 3 NOUVEAUX LIEUX ---
        // LIEU 3
        $lieu3 = new Lieu();
        $lieu3->setNomLieu("Hôpital Fattouma Bourguiba");
        $lieu3->setAdresse("Avenue Farhat Hached");
        $lieu3->setVille("Monastir");
        $lieu3->setCodePostal("5000");
        $manager->persist($lieu3);

        // LIEU 4
        $lieu4 = new Lieu();
        $lieu4->setNomLieu("Lycée Pilote Sousse");
        $lieu4->setAdresse("Boulevard du 7 Novembre");
        $lieu4->setVille("Sousse");
        $lieu4->setCodePostal("4000");
        $manager->persist($lieu4);

        // LIEU 5
        $lieu5 = new Lieu();
        $lieu5->setNomLieu("Centre Culturel Ksar Hellal");
        $lieu5->setAdresse("Rue de la République");
        $lieu5->setVille("Ksar Hellal");
        $lieu5->setCodePostal("5070");
        $manager->persist($lieu5);


        // --- 2. Création des Collectes (Maintenant 3) ---
        
        // COLLECTE 1 (Lieu 1)
        $collecte1 = new Collecte();
        $collecte1->setNom("Collecte spéciale ISET Sousse");
        $collecte1->setDateDebut(new DateTime('+3 days 09:00:00')); 
        $collecte1->setDateFin(new DateTime('+3 days 16:00:00'));
        $collecte1->setCapaciteMaximale(100);
        $collecte1->setStatut("Planifiée");
        $collecte1->setLieu($lieu1); 
        $manager->persist($collecte1);

        // COLLECTE 2 (Lieu 2)
        $collecte2 = new Collecte();
        $collecte2->setNom("Grande collecte FSM");
        $collecte2->setDateDebut(new DateTime('+10 days 09:00:00')); 
        $collecte2->setDateFin(new DateTime('+10 days 17:00:00'));
        $collecte2->setCapaciteMaximale(200);
        $collecte2->setStatut("Planifiée");
        $collecte2->setLieu($lieu2); 
        $manager->persist($collecte2);
        
        // NOUVELLE COLLECTE
        // COLLECTE 3 (Lieu 3)
        $collecte3 = new Collecte();
        $collecte3->setNom("Urgence Hôpital Monastir");
        $collecte3->setDateDebut(new DateTime('+1 days 08:00:00')); // Dans 1 jour
        $collecte3->setDateFin(new DateTime('+1 days 18:00:00'));
        $collecte3->setCapaciteMaximale(300);
        $collecte3->setStatut("Planifiée");
        $collecte3->setLieu($lieu3); // Liée à l'hôpital
        $manager->persist($collecte3);


        // --- 3. Création des Stocks (ça ne change pas) ---
        $groupes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        
        foreach ($groupes as $groupe) {
            $stock = new Stock();
            $stock->setGroupeSanguin($groupe);
            $stock->setNiveauActuel(rand(20, 80)); 
            $stock->setNiveauAlerte(50); 
            $stock->setDerniereMiseAJour(new DateTime());
            
            $manager->persist($stock);
        }

        // --- 4. Exécution ---
        $manager->flush();
    }
}