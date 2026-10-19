<?php

namespace App\Controller;

use App\Entity\LigneCommande;
use App\Entity\Commande;
use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/ligne-commandes')]
class LigneCommandeController extends AbstractController
{
    #[Route('/{id}', name: 'ligne_commande_show', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $em): JsonResponse
    {
        $ligneCommande = $em->getRepository(LigneCommande::class)->find($id);
        
        if (!$ligneCommande) {
            return $this->json(['error' => 'Ligne de commande introuvable'], 404);
        }

        return $this->json([
            'id' => $ligneCommande->getId(),
            'quantite' => $ligneCommande->getQuantite(),
            'prix_unitaire' => $ligneCommande->getPrixUnitaire(),
            'sous_total' => $ligneCommande->getSousTotal(),
            'commande_id' => $ligneCommande->getCommande()->getId(),
            'produit' => [
                'id' => $ligneCommande->getProduit()->getId(),
                'nom' => $ligneCommande->getProduit()->getNom(),
                'reference' => $ligneCommande->getProduit()->getReference()
            ]
        ]);
    }

    #[Route('/{id}', name: 'ligne_commande_update', methods: ['PUT'])]
    public function update(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $ligneCommande = $em->getRepository(LigneCommande::class)->find($id);
        
        if (!$ligneCommande) {
            return $this->json(['error' => 'Ligne de commande introuvable'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['quantite'])) {
            $ligneCommande->setQuantite($data['quantite']);
        }

        if (isset($data['prix_unitaire'])) {
            $ligneCommande->setPrixUnitaire($data['prix_unitaire']);
        }

        // Recalculer le sous-total si quantité ou prix unitaire changent
        if (isset($data['quantite']) || isset($data['prix_unitaire'])) {
            $quantite = $data['quantite'] ?? $ligneCommande->getQuantite();
            $prixUnitaire = $data['prix_unitaire'] ?? $ligneCommande->getPrixUnitaire();
            $ligneCommande->setSousTotal(bcmul($prixUnitaire, $quantite, 2));
        }

        $em->flush();

        // Recalculer le montant total de la commande parente
        $this->recalculerMontantTotal($ligneCommande->getCommande(), $em);

        return $this->json(['message' => 'Ligne de commande mise à jour avec succès']);
    }

    #[Route('/{id}', name: 'ligne_commande_delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em): JsonResponse
    {
        $ligneCommande = $em->getRepository(LigneCommande::class)->find($id);
        
        if (!$ligneCommande) {
            return $this->json(['error' => 'Ligne de commande introuvable'], 404);
        }

        $commande = $ligneCommande->getCommande();
        $em->remove($ligneCommande);
        $em->flush();

        // Recalculer le montant total de la commande parente
        $this->recalculerMontantTotal($commande, $em);

        return $this->json(['message' => 'Ligne de commande supprimée avec succès']);
    }

    private function recalculerMontantTotal(Commande $commande, EntityManagerInterface $em): void
    {
        $total = '0.00';
        foreach ($commande->getLigneCommandes() as $ligne) {
            $total = bcadd($total, $ligne->getSousTotal(), 2);
        }
        $commande->setMontantTotal($total);
        $em->flush();
    }
}