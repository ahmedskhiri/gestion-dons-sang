<?php
// src/Controller/DonateurController.php

namespace App\Controller;

use App\Entity\Collecte;
use App\Entity\RendezVous;
use App\Repository\DonRepository;
use App\Repository\CollecteRepository;
use App\Repository\RendezVousRepository;
use App\Service\DonationEligibilityService; // <-- N'oubliez pas cette ligne
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/donateur', name: 'app_donateur_')]
#[IsGranted('ROLE_DONATEUR')] // Contrôle d'accès général
final class DonateurController extends AbstractController
{
    // Injection par constructeur (recommandé)
    public function __construct(
        private readonly DonationEligibilityService $eligibilityService, 
        private readonly CollecteRepository $collecteRepository
    ) {
    }

    /**
     * Tableau de Bord du Donateur.
     */
    #[Route('/dashboard', name: 'dashboard')]
    public function dashboard(
        RendezVousRepository $rendezVousRepository
    ): Response {
        /** @var \App\Entity\Donateur $donateur */
        $donateur = $this->getUser();

        // 1. Calcul d'éligibilité
        $eligibility = $this->eligibilityService->calculateNextEligibleDate($donateur);

        // 2. Recherche du prochain rendez-vous
        $nextRendezVous = $rendezVousRepository->createQueryBuilder('r')
            ->where('r.donateur = :donateur')
            ->andWhere('r.statut IN (:statuts_actifs)') 
            ->setParameter('donateur', $donateur->getId())
            ->setParameter('statuts_actifs', ['Confirmé', 'Planifié'])
            ->orderBy('r.dateHeureDebut', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $this->render('donateur/dashboard.html.twig', [
            // Note: La variable s'appelle 'nextRendezVous' dans ce template
            'nextRendezVous' => $nextRendezVous, 
            'eligibility' => $eligibility,
        ]);
    }

    /**
     * Action pour annuler un rendez-vous.
     */
    #[Route('/rdv/{id}/cancel', name: 'rdv_cancel', methods: ['POST'])]
    public function cancelRendezVous(
        RendezVous $rendezVous,
        EntityManagerInterface $entityManager
    ): Response {
        /** @var \App\Entity\Donateur $donateur */
        $donateur = $this->getUser();

        if ($rendezVous->getDonateur() !== $donateur) {
            throw $this->createAccessDeniedException();
        }

        $rendezVous->setStatut('Annulé');
        $entityManager->flush();

        $this->addFlash('success', 'Votre rendez-vous a été annulé avec succès.');

        return $this->redirectToRoute('app_donateur_dashboard');
    }

    /**
     * Historique des dons.
     */
    #[Route('/historique', name: 'historique')]
    public function historique(DonRepository $donRepository): Response
    {
        /** @var \App\Entity\Donateur $donateur */
        $donateur = $this->getUser();

        $dons = $donRepository->findBy(['donateur' => $donateur], ['dateDon' => 'DESC']);

        return $this->render('donateur/historique.html.twig', [
            'dons' => $dons,
        ]);
    }

    /**
     * Prise de Rendez-vous.
     */
    #[Route('/rdv/new/{collecteId}', name: 'rdv_new')]
    public function newRendezVous(
        int $collecteId,
        EntityManagerInterface $entityManager
    ): Response {
        /** @var \App\Entity\Donateur $donateur */
        $donateur = $this->getUser();
        
        $collecte = $this->collecteRepository->find($collecteId);

        if (!$collecte) {
            throw $this->createNotFoundException('Collecte introuvable.');
        }

        // 1. Vérification d'éligibilité 
        if (!$this->eligibilityService->calculateNextEligibleDate($donateur)['eligible']) {
             $this->addFlash('danger', "Vous n'êtes pas encore éligible pour un don.");
             return $this->redirectToRoute('app_donateur_dashboard');
        }
        
        // 2. Création du RendezVous
        $rendezVous = new RendezVous();
        $rendezVous->setDonateur($donateur);
        $rendezVous->setCollecte($collecte);
        
        // Créneau de 1h
        $rendezVous->setDateHeureDebut($collecte->getDateDebut());
        $end = clone $collecte->getDateDebut();
        $end->add(new \DateInterval('PT1H')); 
        $rendezVous->setDateHeureFin($end);
        $rendezVous->setStatut('Planifiée'); // Changé à 'Planifiée' pour plus de précision

        $entityManager->persist($rendezVous);
        $entityManager->flush();

        $this->addFlash('success', 'Votre rendez-vous a été pris pour la collecte ' . $collecte->getNom() . '.');

        return $this->redirectToRoute('app_donateur_dashboard');
    }
}