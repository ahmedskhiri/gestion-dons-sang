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
        $collectes = $collecteRepository->findAll();

        // 2. On envoie les collectes au template (la vue)
        return $this->render('public/index.html.twig', [
            'controller_name' => 'PublicController',
            'collectes' => $collectes, // On ajoute cette ligne
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
        
        // 2. Récupérer le filtre (on filtre par l'ID du lieu)
        $lieuId = $request->query->get('lieu');

        $collectes = [];
        
        if ($lieuId) {
            // 3. Si un filtre est actif, on cherche par 'lieu'
            $collectes = $collecteRepository->findBy(['lieu' => $lieuId]);
        } else {
            // 3. Sinon, on les prend toutes
            $collectes = $collecteRepository->findAll();
        }

        // 4. On récupère tous les lieux pour le menu déroulant
        $lieux = $lieuRepository->findAll();

        return $this->render('public/liste_collectes.html.twig', [
            'collectes' => $collectes,
            'lieux' => $lieux, // On envoie les lieux au template
            'lieuFiltreId' => $lieuId // On envoie l'ID sélectionné
        ]);
    }
}
