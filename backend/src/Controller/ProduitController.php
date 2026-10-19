<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Categorie;
use App\Entity\Fournisseur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/produits')]
class ProduitController extends AbstractController
{
    #[Route('', name: 'produits_list', methods: ['GET'])]
public function index(EntityManagerInterface $em): JsonResponse
{
    $produits = $em->getRepository(Produit::class)->findAll();
    
    $data = array_map(function($produit) {
        return [
            'id' => $produit->getId(),
            'nom' => $produit->getNom(),
            'reference' => $produit->getReference(),
            'description' => $produit->getDescription(),
            'prix_unitaire' => $produit->getPrixUnitaire(),
            'quantite_stock' => $produit->getQuantiteStock(),
            'seuil_alerte' => $produit->getSeuilAlerte(),
            'stock_bas' => $produit->isStockBas(), // ← Cette ligne nécessite la méthode
            'categorie' => [
                'id' => $produit->getCategorie()?->getId(),
                'nom' => $produit->getCategorie()?->getNom()
            ],
            'fournisseur' => $produit->getFournisseur() ? [
                'id' => $produit->getFournisseur()->getId(),
                'nom' => $produit->getFournisseur()->getNom()
            ] : null,
            'date_creation' => $produit->getDateCreation()?->format('Y-m-d H:i:s'),
            'date_modification' => $produit->getDateModification()?->format('Y-m-d H:i:s')
        ];
    }, $produits);

    return $this->json($data);
}

    #[Route('/{id}', name: 'produit_show', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $em): JsonResponse
    {
        $produit = $em->getRepository(Produit::class)->find($id);
        
        if (!$produit) {
            return $this->json(['error' => 'Produit introuvable'], 404);
        }

        return $this->json([
            'id' => $produit->getId(),
            'nom' => $produit->getNom(),
            'reference' => $produit->getReference(),
            'description' => $produit->getDescription(),
            'prix_unitaire' => $produit->getPrixUnitaire(),
            'quantite_stock' => $produit->getQuantiteStock(),
            'seuil_alerte' => $produit->getSeuilAlerte(),
            'stock_bas' => $produit->isStockBas(),
            'categorie' => [
                'id' => $produit->getCategorie()?->getId(),
                'nom' => $produit->getCategorie()?->getNom()
            ],
            'fournisseur' => $produit->getFournisseur() ? [
                'id' => $produit->getFournisseur()->getId(),
                'nom' => $produit->getFournisseur()->getNom()
            ] : null
        ]);
    }

    #[Route('/api/dashboard/stats', name: 'app_stats', methods: ['GET'])]
public function getStats(): JsonResponse
{
    return $this->json([
        'totalProduits' => 24,
        'stockFaible' => 3,
        'categories' => 5
    ]);
}
    #[Route('', name: 'produit_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validation
        if (!isset($data['nom']) || !isset($data['reference']) || !isset($data['prix_unitaire']) || !isset($data['categorie_id'])) {
            return $this->json(['error' => 'Données manquantes (nom, reference, prix_unitaire, categorie_id requis)'], 400);
        }

        // Vérifier si la référence existe déjà
        $existingProduit = $em->getRepository(Produit::class)->findOneBy(['reference' => $data['reference']]);
        if ($existingProduit) {
            return $this->json(['error' => 'Cette référence existe déjà'], 409);
        }

        // Vérifier la catégorie
        $categorie = $em->getRepository(Categorie::class)->find($data['categorie_id']);
        if (!$categorie) {
            return $this->json(['error' => 'Catégorie introuvable'], 404);
        }

        // Créer le produit
        $produit = new Produit();
        $produit->setNom($data['nom']);
        $produit->setReference($data['reference']);
        $produit->setDescription($data['description'] ?? null);
        $produit->setPrixUnitaire($data['prix_unitaire']);
        $produit->setQuantiteStock($data['quantite_stock'] ?? 0);
        $produit->setSeuilAlerte($data['seuil_alerte'] ?? 10);
        $produit->setCategorie($categorie);

        // Fournisseur (optionnel)

        if (isset($data['fournisseur_id'])) {
            $fournisseur = $em->getRepository(Fournisseur::class)->find($data['fournisseur_id']);
            if ($fournisseur) {
                $produit->setFournisseur($fournisseur);
            }
        }

        $em->persist($produit);
        $em->flush();

        return $this->json([
            'message' => 'Produit créé avec succès',
            'id' => $produit->getId()
        ], 201);
    }

    #[Route('/{id}', name: 'produit_update', methods: ['PUT'])]
    public function update(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $produit = $em->getRepository(Produit::class)->find($id);
        
        if (!$produit) {
            return $this->json(['error' => 'Produit introuvable'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['nom'])) $produit->setNom($data['nom']);
        if (isset($data['description'])) $produit->setDescription($data['description']);
        if (isset($data['prix_unitaire'])) $produit->setPrixUnitaire($data['prix_unitaire']);
        if (isset($data['quantite_stock'])) $produit->setQuantiteStock($data['quantite_stock']);
        if (isset($data['seuil_alerte'])) $produit->setSeuilAlerte($data['seuil_alerte']);

        if (isset($data['categorie_id'])) {
            $categorie = $em->getRepository(Categorie::class)->find($data['categorie_id']);
            if ($categorie) {
                $produit->setCategorie($categorie);
            }
        }

        if (isset($data['fournisseur_id'])) {
            $fournisseur = $em->getRepository(Fournisseur::class)->find($data['fournisseur_id']);
            $produit->setFournisseur($fournisseur);
        }

        $em->flush();

        return $this->json(['message' => 'Produit mis à jour avec succès']);
    }

    #[Route('/{id}', name: 'produit_delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em): JsonResponse
    {
        $produit = $em->getRepository(Produit::class)->find($id);
        
        if (!$produit) {
            return $this->json(['error' => 'Produit introuvable'], 404);
        }

        $em->remove($produit);
        $em->flush();

        return $this->json(['message' => 'Produit supprimé avec succès']);
    }

    #[Route('/stock-bas', name: 'produits_stock_bas', methods: ['GET'])]
    public function stockBas(EntityManagerInterface $em): JsonResponse
    {
        $produits = $em->getRepository(Produit::class)->findAll();
        
        $produitsStockBas = array_filter($produits, function($produit) {
            return $produit->isStockBas();
        });

        $data = array_map(function($produit) {
            return [
                'id' => $produit->getId(),
                'nom' => $produit->getNom(),
                'reference' => $produit->getReference(),
                'quantite_stock' => $produit->getQuantiteStock(),
                'seuil_alerte' => $produit->getSeuilAlerte(),
            ];
        }, $produitsStockBas);

        return $this->json(array_values($data));
    }
}