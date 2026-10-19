<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Commande;
use App\Entity\Alerte;
use App\Entity\Categorie;
use App\Entity\Fournisseur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/api/dashboard', name: 'dashboard', methods: ['GET'])]
    public function dashboard(EntityManagerInterface $em): JsonResponse
    {
        // Statistiques générales
        $totalProduits = $em->getRepository(Produit::class)->count([]);
        $totalCategories = $em->getRepository(Categorie::class)->count([]);
        $totalFournisseurs = $em->getRepository(Fournisseur::class)->count([]);
        $totalCommandes = $em->getRepository(Commande::class)->count([]);

        // Valeur totale du stock
        $produits = $em->getRepository(Produit::class)->findAll();
        $valeurStock = 0;
        foreach ($produits as $produit) {
            $valeurStock += floatval($produit->getPrixUnitaire()) * $produit->getQuantiteStock();
        }

        // Produits en stock bas
        $produitsStockBas = array_filter($produits, function($p) {
            return $p->isStockBas();
        });

        // Alertes non lues
        $alertesNonLues = $em->getRepository(Alerte::class)->count(['lu' => false]);

        // Commandes en attente
        $commandesEnAttente = $em->getRepository(Commande::class)->count(['statut' => 'en_attente']);

        // Top 5 produits les plus vendus (ou les plus consultés)
        $topProduits = array_slice($produits, 0, 5);

   return $this->json([
    'totalProduits' => $totalProduits,
    'totalCategories' => $totalCategories,
    'totalFournisseurs' => $totalFournisseurs,
    'totalCommandes' => $totalCommandes,
    'valeurStock' => round($valeurStock, 2),
    'produitsStockBas' => count($produitsStockBas),
    'alertesNonLues' => $alertesNonLues,
    'commandesEnAttente' => $commandesEnAttente,
    'topProduits' => [] 
]);
    }
}