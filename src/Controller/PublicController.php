<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CollecteRepository;
use App\Repository\StockRepository;
use App\Repository\LieuRepository;
use Symfony\Component\HttpFoundation\Request;

final class PublicController extends AbstractController
{
    #[Route('/', name: 'app_Acceuil')]
    public function index(CollecteRepository $collecteRepository): Response
    {
        // Récupérer les prochaines collectes (statut Planifiée, date future)
        $now = new \DateTime();
        $collectes = $collecteRepository->createQueryBuilder('c')
            ->where('c.statut = :statut')
            ->andWhere('c.dateDebut >= :now')
            ->setParameter('statut', 'Planifiée')
            ->setParameter('now', $now)
            ->orderBy('c.dateDebut', 'ASC')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult();

        return $this->render('public/index.html.twig', [
            'collectes' => $collectes,
        ]);
    }
    #[Route('/stocks', name: 'app_stocks')]
    public function stocks(StockRepository $stockRepository): Response
    {
        // 2. Récupère tous les stocks
        $stocks = $stockRepository->findAll();

        // 3. Envoie-les au nouveau template
        return $this->render('public/stocks.html.twig', [
            'stocks' => $stocks,
        ]);
    }
    #[Route('/collectes', name: 'app_collectes_liste')]
    public function listeCollectes(
        CollecteRepository $collecteRepository,
        LieuRepository $lieuRepository,
        Request $request
    ): Response {
        // Récupérer les filtres
        $lieuId = $request->query->get('lieu');
        $dateDebut = $request->query->get('date_debut');
        $dateFin = $request->query->get('date_fin');
        $ville = $request->query->get('ville');

        // Construction de la requête avec filtres
        $qb = $collecteRepository->createQueryBuilder('c')
            ->leftJoin('c.lieu', 'l')
            ->where('c.statut = :statut')
            ->setParameter('statut', 'Planifiée');

        if ($lieuId) {
            $qb->andWhere('c.lieu = :lieuId')
               ->setParameter('lieuId', $lieuId);
        }

        if ($ville) {
            $qb->andWhere('l.ville LIKE :ville')
               ->setParameter('ville', '%' . $ville . '%');
        }

        if ($dateDebut) {
            $qb->andWhere('c.dateDebut >= :dateDebut')
               ->setParameter('dateDebut', new \DateTime($dateDebut));
        }

        if ($dateFin) {
            $qb->andWhere('c.dateFin <= :dateFin')
               ->setParameter('dateFin', new \DateTime($dateFin));
        }

        $collectes = $qb->orderBy('c.dateDebut', 'ASC')
                        ->getQuery()
                        ->getResult();

        // Récupérer tous les lieux et villes uniques pour les filtres
        $lieux = $lieuRepository->findAll();
        $villes = $lieuRepository->createQueryBuilder('l')
            ->select('DISTINCT l.ville')
            ->orderBy('l.ville', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('public/liste_collectes.html.twig', [
            'collectes' => $collectes,
            'lieux' => $lieux,
            'villes' => array_column($villes, 'ville'),
            'lieuFiltreId' => $lieuId,
            'villeFiltre' => $ville,
            'dateDebutFiltre' => $dateDebut,
            'dateFinFiltre' => $dateFin,
        ]);
    }
}
