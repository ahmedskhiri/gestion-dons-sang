<?php

namespace App\Controller;

use App\Entity\Collecte;
use App\Entity\Don;
use App\Entity\Lieu;
use App\Entity\RendezVous;
use App\Entity\Stock;
use App\Form\CollecteType;
use App\Form\DonValidationType;
use App\Form\LieuType;
use App\Repository\CollecteRepository;
use App\Repository\DonateurRepository;
use App\Repository\LieuRepository;
use App\Repository\RendezVousRepository;
use App\Repository\StockRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
final class AdminController extends AbstractController
{
    // ==================== DASHBOARD ====================
    
    #[Route('', name: 'app_admin_dashboard')]
    public function dashboard(
        DonateurRepository $donateurRepository,
        StockRepository $stockRepository,
        RendezVousRepository $rendezVousRepository
    ): Response {
        // Statistics
        $totalDonateurs = $donateurRepository->count([]);
        
        // Stocks at critical level (niveauActuel < niveauAlerte)
        $stocksCritiques = $stockRepository->createQueryBuilder('s')
            ->where('s.niveauActuel < s.niveauAlerte')
            ->getQuery()
            ->getResult();
        $nbStocksCritiques = count($stocksCritiques);
        
        // Rendez-vous waiting to be validated (status "Effectué" without Don)
        $rdvAValider = $rendezVousRepository->createQueryBuilder('r')
            ->leftJoin('r.don', 'd')
            ->where('r.statut = :statut')
            ->andWhere('d.id IS NULL')
            ->setParameter('statut', 'Effectué')
            ->getQuery()
            ->getResult();
        $nbRdvAValider = count($rdvAValider);
        
        return $this->render('admin/dashboard.html.twig', [
            'totalDonateurs' => $totalDonateurs,
            'nbStocksCritiques' => $nbStocksCritiques,
            'nbRdvAValider' => $nbRdvAValider,
        ]);
    }

    // ==================== LIEU CRUD ====================
    
    #[Route('/lieu', name: 'app_admin_lieu_index', methods: ['GET'])]
    public function lieuIndex(LieuRepository $lieuRepository): Response
    {
        return $this->render('admin/lieu/index.html.twig', [
            'lieux' => $lieuRepository->findAll(),
        ]);
    }

    #[Route('/lieu/new', name: 'app_admin_lieu_new', methods: ['GET', 'POST'])]
    public function lieuNew(Request $request, EntityManagerInterface $entityManager): Response
    {
        $lieu = new Lieu();
        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($lieu);
            $entityManager->flush();

            $this->addFlash('success', 'Le lieu a été créé avec succès.');
            return $this->redirectToRoute('app_admin_lieu_index');
        }

        return $this->render('admin/lieu/new.html.twig', [
            'lieu' => $lieu,
            'form' => $form,
        ]);
    }

    #[Route('/lieu/{id}', name: 'app_admin_lieu_show', methods: ['GET'])]
    public function lieuShow(Lieu $lieu): Response
    {
        return $this->render('admin/lieu/show.html.twig', [
            'lieu' => $lieu,
        ]);
    }

    #[Route('/lieu/{id}/edit', name: 'app_admin_lieu_edit', methods: ['GET', 'POST'])]
    public function lieuEdit(Request $request, Lieu $lieu, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le lieu a été modifié avec succès.');
            return $this->redirectToRoute('app_admin_lieu_index');
        }

        return $this->render('admin/lieu/edit.html.twig', [
            'lieu' => $lieu,
            'form' => $form,
        ]);
    }

    #[Route('/lieu/{id}', name: 'app_admin_lieu_delete', methods: ['POST'])]
    public function lieuDelete(Request $request, Lieu $lieu, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$lieu->getId(), $request->request->get('_token'))) {
            $entityManager->remove($lieu);
            $entityManager->flush();
            $this->addFlash('success', 'Le lieu a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_admin_lieu_index');
    }

    // ==================== COLLECTE CRUD ====================
    
    #[Route('/collecte', name: 'app_admin_collecte_index', methods: ['GET'])]
    public function collecteIndex(CollecteRepository $collecteRepository): Response
    {
        return $this->render('admin/collecte/index.html.twig', [
            'collectes' => $collecteRepository->findAll(),
        ]);
    }

    #[Route('/collecte/new', name: 'app_admin_collecte_new', methods: ['GET', 'POST'])]
    public function collecteNew(Request $request, EntityManagerInterface $entityManager): Response
    {
        $collecte = new Collecte();
        $form = $this->createForm(CollecteType::class, $collecte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($collecte);
            $entityManager->flush();

            $this->addFlash('success', 'La collecte a été créée avec succès.');
            return $this->redirectToRoute('app_admin_collecte_index');
        }

        return $this->render('admin/collecte/new.html.twig', [
            'collecte' => $collecte,
            'form' => $form,
        ]);
    }

    #[Route('/collecte/{id}', name: 'app_admin_collecte_show', methods: ['GET'])]
    public function collecteShow(Collecte $collecte): Response
    {
        return $this->render('admin/collecte/show.html.twig', [
            'collecte' => $collecte,
        ]);
    }

    #[Route('/collecte/{id}/edit', name: 'app_admin_collecte_edit', methods: ['GET', 'POST'])]
    public function collecteEdit(Request $request, Collecte $collecte, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CollecteType::class, $collecte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'La collecte a été modifiée avec succès.');
            return $this->redirectToRoute('app_admin_collecte_index');
        }

        return $this->render('admin/collecte/edit.html.twig', [
            'collecte' => $collecte,
            'form' => $form,
        ]);
    }

    #[Route('/collecte/{id}', name: 'app_admin_collecte_delete', methods: ['POST'])]
    public function collecteDelete(Request $request, Collecte $collecte, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$collecte->getId(), $request->request->get('_token'))) {
            $entityManager->remove($collecte);
            $entityManager->flush();
            $this->addFlash('success', 'La collecte a été supprimée avec succès.');
        }

        return $this->redirectToRoute('app_admin_collecte_index');
    }

    // ==================== STOCK MANAGEMENT ====================
    
    #[Route('/stock', name: 'app_admin_stock_index', methods: ['GET'])]
    public function stockIndex(StockRepository $stockRepository): Response
    {
        $stocks = $stockRepository->findAll();
        return $this->render('admin/stock/index.html.twig', [
            'stocks' => $stocks,
        ]);
    }

    #[Route('/stock/{id}/update', name: 'app_admin_stock_update', methods: ['POST'])]
    public function stockUpdate(Request $request, Stock $stock, EntityManagerInterface $entityManager): Response
    {
        $nouveauNiveau = $request->request->get('niveauActuel');
        
        if ($nouveauNiveau !== null && is_numeric($nouveauNiveau)) {
            $stock->setNiveauActuel((int) $nouveauNiveau);
            $stock->setDerniereMiseAJour(new \DateTime());
            $entityManager->flush();
            
            $this->addFlash('success', 'Le niveau de stock a été mis à jour avec succès.');
        } else {
            $this->addFlash('danger', 'Valeur invalide pour le niveau de stock.');
        }

        return $this->redirectToRoute('app_admin_stock_index');
    }

    // ==================== DONATION VALIDATION ====================
    
    #[Route('/don/valider', name: 'app_admin_don_valider', methods: ['GET'])]
    public function donValiderList(RendezVousRepository $rendezVousRepository): Response
    {
        // Rendez-vous with status "Effectué" that don't have an associated Don
        $rdvAValider = $rendezVousRepository->createQueryBuilder('r')
            ->leftJoin('r.don', 'd')
            ->where('r.statut = :statut')
            ->andWhere('d.id IS NULL')
            ->setParameter('statut', 'Effectué')
            ->orderBy('r.dateHeureDebut', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('admin/don/valider_list.html.twig', [
            'rdvAValider' => $rdvAValider,
        ]);
    }

    #[Route('/don/valider/{id}', name: 'app_admin_don_valider_form', methods: ['GET', 'POST'])]
    public function donValiderForm(
        Request $request,
        RendezVous $rendezVous,
        EntityManagerInterface $entityManager
    ): Response {
        // Verify that this rendez-vous is eligible for validation
        if ($rendezVous->getStatut() !== 'Effectué' || $rendezVous->getDon() !== null) {
            $this->addFlash('danger', 'Ce rendez-vous ne peut pas être validé.');
            return $this->redirectToRoute('app_admin_don_valider');
        }

        $don = new Don();
        $don->setRendezVous($rendezVous);
        $don->setDonateur($rendezVous->getDonateur());
        $don->setDateDon(new \DateTime());

        $form = $this->createForm(DonValidationType::class, $don);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Set bidirectional relationship
            $rendezVous->setDon($don);
            
            // Create the Don entity
            $entityManager->persist($don);
            
            // Update Donateur's derniereDateDon
            $donateur = $rendezVous->getDonateur();
            $donateur->setDernierDateDon(new \DateTime());
            
            $entityManager->flush();

            $this->addFlash('success', 'Le don a été validé avec succès.');
            return $this->redirectToRoute('app_admin_don_valider');
        }

        return $this->render('admin/don/valider_form.html.twig', [
            'rendezVous' => $rendezVous,
            'form' => $form,
        ]);
    }
}

