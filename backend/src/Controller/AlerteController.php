<?php

namespace App\Controller;

use App\Entity\Alerte;
use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/alertes')]
class AlerteController extends AbstractController
{
    #[Route('', name: 'alertes_list', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $nonLues = $request->query->get('non_lues');
        
        if ($nonLues === 'true') {
            $alertes = $em->getRepository(Alerte::class)->findBy(
                ['lu' => false],
                ['dateAlerte' => 'DESC']
            );
        } else {
            $alertes = $em->getRepository(Alerte::class)->findBy(
                [],
                ['dateAlerte' => 'DESC']
            );
        }

        $data = array_map(function($alerte) {
            return [
                'id' => $alerte->getId(),
                'type' => $alerte->getType(),
                'message' => $alerte->getMessage(),
                'date_alerte' => $alerte->getDateAlerte()?->format('Y-m-d H:i:s'),
                'lu' => $alerte->isLu(),
                'produit' => [
                    'id' => $alerte->getProduit()->getId(),
                    'nom' => $alerte->getProduit()->getNom(),
                    'reference' => $alerte->getProduit()->getReference(),
                    'quantite_stock' => $alerte->getProduit()->getQuantiteStock()
                ]
            ];
        }, $alertes);

        return $this->json($data);
    }

    #[Route('/generer', name: 'alertes_generer', methods: ['POST'])]
    public function genererAlertes(EntityManagerInterface $em): JsonResponse
    {
        $produits = $em->getRepository(Produit::class)->findAll();
        $alertesCreees = 0;

        foreach ($produits as $produit) {
            // Vérifier si une alerte existe déjà pour ce produit
            $alerteExistante = $em->getRepository(Alerte::class)->findOneBy([
                'produit' => $produit,
                'lu' => false
            ]);

            if ($alerteExistante) {
                continue; // Ne pas créer de doublon
            }

            // Stock bas
            if ($produit->getQuantiteStock() <= $produit->getSeuilAlerte() && $produit->getQuantiteStock() > 0) {
                $alerte = new Alerte();
                $alerte->setProduit($produit);
                $alerte->setType('stock_bas');
                $alerte->setMessage(
                    sprintf(
                        'Le produit "%s" est en stock bas (%d unités restantes, seuil: %d)',
                        $produit->getNom(),
                        $produit->getQuantiteStock(),
                        $produit->getSeuilAlerte()
                    )
                );
                
                $em->persist($alerte);
                $alertesCreees++;
            }

            // Rupture de stock
            if ($produit->getQuantiteStock() === 0) {
                $alerte = new Alerte();
                $alerte->setProduit($produit);
                $alerte->setType('rupture');
                $alerte->setMessage(
                    sprintf('Le produit "%s" est en rupture de stock', $produit->getNom())
                );
                
                $em->persist($alerte);
                $alertesCreees++;
            }
        }

        $em->flush();

        return $this->json([
            'message' => 'Alertes générées avec succès',
            'nombre_alertes' => $alertesCreees
        ]);
    }

    #[Route('/{id}/lire', name: 'alerte_marquer_lue', methods: ['PATCH'])]
    public function marquerLue(int $id, EntityManagerInterface $em): JsonResponse
    {
        $alerte = $em->getRepository(Alerte::class)->find($id);
        
        if (!$alerte) {
            return $this->json(['error' => 'Alerte introuvable'], 404);
        }

        $alerte->setLu(true);
        $em->flush();

        return $this->json(['message' => 'Alerte marquée comme lue']);
    }

    #[Route('/tout-lire', name: 'alertes_tout_lire', methods: ['PATCH'])]
    public function toutLire(EntityManagerInterface $em): JsonResponse
    {
        $alertes = $em->getRepository(Alerte::class)->findBy(['lu' => false]);

        foreach ($alertes as $alerte) {
            $alerte->setLu(true);
        }

        $em->flush();

        return $this->json([
            'message' => 'Toutes les alertes ont été marquées comme lues',
            'nombre' => count($alertes)
        ]);
    }

    #[Route('/{id}', name: 'alerte_delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em): JsonResponse
    {
        $alerte = $em->getRepository(Alerte::class)->find($id);
        
        if (!$alerte) {
            return $this->json(['error' => 'Alerte introuvable'], 404);
        }

        $em->remove($alerte);
        $em->flush();

        return $this->json(['message' => 'Alerte supprimée avec succès']);
    }
}