<?php

namespace App\Controller;

use App\Entity\MouvementStock;
use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/mouvements')]
class MouvementStockController extends AbstractController
{
    #[Route('', name: 'mouvements_list', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $produitId = $request->query->get('produit_id');
        $type = $request->query->get('type');
        
        $qb = $em->getRepository(MouvementStock::class)->createQueryBuilder('m')
            ->orderBy('m.dateMouvement', 'DESC');

        if ($produitId) {
            $qb->andWhere('m.produit = :produit')
               ->setParameter('produit', $produitId);
        }

        if ($type) {
            $qb->andWhere('m.type = :type')
               ->setParameter('type', $type);
        }

        $mouvements = $qb->getQuery()->getResult();
        
        $data = array_map(function($mouvement) {
            return [
                'id' => $mouvement->getId(),
                'produit' => [
                    'id' => $mouvement->getProduit()->getId(),
                    'nom' => $mouvement->getProduit()->getNom(),
                    'reference' => $mouvement->getProduit()->getReference()
                ],
                'type' => $mouvement->getType(),
                'quantite' => $mouvement->getQuantite(),
                'motif' => $mouvement->getMotif(),
                'date_mouvement' => $mouvement->getDateMouvement()?->format('Y-m-d H:i:s'),
                'commentaire' => $mouvement->getCommentaire(),
                'commande_id' => $mouvement->getCommande()?->getId()
            ];
        }, $mouvements);

        return $this->json($data);
    }

    #[Route('/produit/{id}', name: 'mouvements_produit', methods: ['GET'])]
    public function byProduit(int $id, EntityManagerInterface $em): JsonResponse
    {
        $produit = $em->getRepository(Produit::class)->find($id);
        
        if (!$produit) {
            return $this->json(['error' => 'Produit introuvable'], 404);
        }

        $mouvements = $em->getRepository(MouvementStock::class)->findBy(
            ['produit' => $produit],
            ['dateMouvement' => 'DESC']
        );

        $data = array_map(function($mouvement) {
            return [
                'id' => $mouvement->getId(),
                'type' => $mouvement->getType(),
                'quantite' => $mouvement->getQuantite(),
                'motif' => $mouvement->getMotif(),
                'date_mouvement' => $mouvement->getDateMouvement()?->format('Y-m-d H:i:s'),
                'commentaire' => $mouvement->getCommentaire()
            ];
        }, $mouvements);

        return $this->json($data);
    }

    #[Route('', name: 'mouvement_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validation
        if (!isset($data['produit_id']) || !isset($data['type']) || !isset($data['quantite']) || !isset($data['motif'])) {
            return $this->json(['error' => 'Données manquantes (produit_id, type, quantite, motif requis)'], 400);
        }

        // Vérifier le produit
        $produit = $em->getRepository(Produit::class)->find($data['produit_id']);
        if (!$produit) {
            return $this->json(['error' => 'Produit introuvable'], 404);
        }

        // Vérifier le type
        if (!in_array($data['type'], ['entree', 'sortie'])) {
            return $this->json(['error' => 'Type invalide (entree ou sortie)'], 400);
        }

        // Créer le mouvement
        $mouvement = new MouvementStock();
        $mouvement->setProduit($produit);
        $mouvement->setType($data['type']);
        $mouvement->setQuantite($data['quantite']);
        $mouvement->setMotif($data['motif']);
        $mouvement->setCommentaire($data['commentaire'] ?? null);

        // Mettre à jour le stock du produit
        if ($data['type'] === 'entree') {
            $nouveauStock = $produit->getQuantiteStock() + $data['quantite'];
        } else {
            $nouveauStock = $produit->getQuantiteStock() - $data['quantite'];
            
            if ($nouveauStock < 0) {
                return $this->json(['error' => 'Stock insuffisant'], 400);
            }
        }

        $produit->setQuantiteStock($nouveauStock);

        $em->persist($mouvement);
        $em->flush();

        return $this->json([
            'message' => 'Mouvement enregistré avec succès',
            'id' => $mouvement->getId(),
            'nouveau_stock' => $produit->getQuantiteStock()
        ], 201);
    }

    #[Route('/{id}', name: 'mouvement_show', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $em): JsonResponse
    {
        $mouvement = $em->getRepository(MouvementStock::class)->find($id);
        
        if (!$mouvement) {
            return $this->json(['error' => 'Mouvement introuvable'], 404);
        }

        return $this->json([
            'id' => $mouvement->getId(),
            'produit' => [
                'id' => $mouvement->getProduit()->getId(),
                'nom' => $mouvement->getProduit()->getNom(),
                'reference' => $mouvement->getProduit()->getReference()
            ],
            'type' => $mouvement->getType(),
            'quantite' => $mouvement->getQuantite(),
            'motif' => $mouvement->getMotif(),
            'date_mouvement' => $mouvement->getDateMouvement()?->format('Y-m-d H:i:s'),
            'commentaire' => $mouvement->getCommentaire(),
            'commande' => $mouvement->getCommande() ? [
                'id' => $mouvement->getCommande()->getId(),
                'numero' => $mouvement->getCommande()->getNumeroCommande()
            ] : null
        ]);
    }
}