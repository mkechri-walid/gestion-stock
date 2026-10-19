<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Entity\Produit;
use App\Entity\Fournisseur;
use App\Entity\MouvementStock;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/commandes')]
class CommandeController extends AbstractController
{
    #[Route('', name: 'commandes_list', methods: ['GET'])]
    public function index(EntityManagerInterface $em): JsonResponse
    {
        $commandes = $em->getRepository(Commande::class)->findAll();
        
        $data = array_map(function($commande) {
            return [
                'id' => $commande->getId(),
                'numero_commande' => $commande->getNumeroCommande(),
                'fournisseur' => [
                    'id' => $commande->getFournisseur()->getId(),
                    'nom' => $commande->getFournisseur()->getNom()
                ],
                'date_commande' => $commande->getDateCommande()?->format('Y-m-d H:i:s'),
                'date_livraison_prevue' => $commande->getDateLivraisonPrevue()?->format('Y-m-d'),
                'statut' => $commande->getStatut(),
                'montant_total' => $commande->getMontantTotal(),
                'nombre_lignes' => $commande->getLignesCommande()->count()
            ];
        }, $commandes);

        return $this->json($data);
    }

    #[Route('/{id}', name: 'commande_show', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $em): JsonResponse
    {
        $commande = $em->getRepository(Commande::class)->find($id);
        
        if (!$commande) {
            return $this->json(['error' => 'Commande introuvable'], 404);
        }

        $lignes = [];
        foreach ($commande->getLignesCommande() as $ligne) {
            $lignes[] = [
                'id' => $ligne->getId(),
                'produit' => [
                    'id' => $ligne->getProduit()->getId(),
                    'nom' => $ligne->getProduit()->getNom(),
                    'reference' => $ligne->getProduit()->getReference()
                ],
                'quantite' => $ligne->getQuantite(),
                'prix_unitaire' => $ligne->getPrixUnitaire(),
                'sous_total' => $ligne->getSousTotal()
            ];
        }

        return $this->json([
            'id' => $commande->getId(),
            'numero_commande' => $commande->getNumeroCommande(),
            'fournisseur' => [
                'id' => $commande->getFournisseur()->getId(),
                'nom' => $commande->getFournisseur()->getNom()
            ],
            'date_commande' => $commande->getDateCommande()?->format('Y-m-d H:i:s'),
            'date_livraison_prevue' => $commande->getDateLivraisonPrevue()?->format('Y-m-d'),
            'statut' => $commande->getStatut(),
            'montant_total' => $commande->getMontantTotal(),
            'lignes' => $lignes
        ]);
    }

    #[Route('', name: 'commande_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validation
        if (!isset($data['fournisseur_id']) || !isset($data['produits']) || empty($data['produits'])) {
            return $this->json(['error' => 'Fournisseur et produits requis'], 400);
        }

        // Vérifier le fournisseur
        $fournisseur = $em->getRepository(Fournisseur::class)->find($data['fournisseur_id']);
        if (!$fournisseur) {
            return $this->json(['error' => 'Fournisseur introuvable'], 404);
        }

        // Créer la commande
        $commande = new Commande();
        $commande->setFournisseur($fournisseur);
        
        if (isset($data['date_livraison_prevue'])) {
            $commande->setDateLivraisonPrevue(new \DateTimeImmutable($data['date_livraison_prevue']));
        }

        // Ajouter les lignes de commande
        foreach ($data['produits'] as $item) {
            if (!isset($item['produit_id']) || !isset($item['quantite'])) {
                continue;
            }

            $produit = $em->getRepository(Produit::class)->find($item['produit_id']);
            if (!$produit) {
                continue;
            }

            $ligne = new LigneCommande();
            $ligne->setProduit($produit);
            $ligne->setQuantite($item['quantite']);
            $ligne->setPrixUnitaire($produit->getPrixUnitaire());
            
            $commande->addLigneCommande($ligne);
        }

        // Calculer le montant total
        $commande->calculerMontantTotal();

        $em->persist($commande);
        $em->flush();

        return $this->json([
            'message' => 'Commande créée avec succès',
            'id' => $commande->getId(),
            'numero_commande' => $commande->getNumeroCommande(),
            'montant_total' => $commande->getMontantTotal()
        ], 201);
    }

    #[Route('/{id}/statut', name: 'commande_update_statut', methods: ['PATCH'])]
    public function updateStatut(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $commande = $em->getRepository(Commande::class)->find($id);
        
        if (!$commande) {
            return $this->json(['error' => 'Commande introuvable'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['statut'])) {
            return $this->json(['error' => 'Statut requis'], 400);
        }

        $ancienStatut = $commande->getStatut();
        $nouveauStatut = $data['statut'];

        $commande->setStatut($nouveauStatut);

        // Si la commande passe à "livrée", mettre à jour le stock
        if ($nouveauStatut === 'livree' && $ancienStatut !== 'livree') {
            foreach ($commande->getLignesCommande() as $ligne) {
                $produit = $ligne->getProduit();
                
                // Mettre à jour le stock
                $nouveauStock = $produit->getQuantiteStock() + $ligne->getQuantite();
                $produit->setQuantiteStock($nouveauStock);

                // Créer un mouvement de stock
                $mouvement = new MouvementStock();
                $mouvement->setProduit($produit);
                $mouvement->setCommande($commande);
                $mouvement->setType('entree');
                $mouvement->setQuantite($ligne->getQuantite());
                $mouvement->setMotif('achat');
                $mouvement->setCommentaire('Livraison commande ' . $commande->getNumeroCommande());

                $em->persist($mouvement);
            }
        }

        $em->flush();

        return $this->json([
            'message' => 'Statut mis à jour avec succès',
            'statut' => $commande->getStatut()
        ]);
    }

    #[Route('/{id}', name: 'commande_delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em): JsonResponse
    {
        $commande = $em->getRepository(Commande::class)->find($id);
        
        if (!$commande) {
            return $this->json(['error' => 'Commande introuvable'], 404);
        }

        if ($commande->getStatut() === 'livree') {
            return $this->json(['error' => 'Impossible de supprimer une commande livrée'], 409);
        }

        $em->remove($commande);
        $em->flush();

        return $this->json(['message' => 'Commande supprimée avec succès']);
    }
}