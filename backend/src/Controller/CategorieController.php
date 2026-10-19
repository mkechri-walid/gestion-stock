<?php

namespace App\Controller;

use App\Entity\Categorie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/categories')]
class CategorieController extends AbstractController
{
    #[Route('', name: 'categories_list', methods: ['GET'])]
    public function index(EntityManagerInterface $em): JsonResponse
    {
        $categories = $em->getRepository(Categorie::class)->findAll();
        
        $data = array_map(function($categorie) {
            return [
                'id' => $categorie->getId(),
                'nom' => $categorie->getNom(),
                'description' => $categorie->getDescription(),
                'nombre_produits' => $categorie->getProduits()->count()
            ];
        }, $categories);

        return $this->json($data);
    }

    #[Route('/{id}', name: 'categorie_show', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $em): JsonResponse
    {
        $categorie = $em->getRepository(Categorie::class)->find($id);
        
        if (!$categorie) {
            return $this->json(['error' => 'Catégorie introuvable'], 404);
        }

        return $this->json([
            'id' => $categorie->getId(),
            'nom' => $categorie->getNom(),
            'description' => $categorie->getDescription(),
            'nombre_produits' => $categorie->getProduits()->count()
        ]);
    }

    #[Route('', name: 'categorie_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['nom'])) {
            return $this->json(['error' => 'Le nom est requis'], 400);
        }

        $categorie = new Categorie();
        $categorie->setNom($data['nom']);
        $categorie->setDescription($data['description'] ?? null);

        $em->persist($categorie);
        $em->flush();

        return $this->json([
            'message' => 'Catégorie créée avec succès',
            'id' => $categorie->getId()
        ], 201);
    }

    #[Route('/{id}', name: 'categorie_update', methods: ['PUT'])]
    public function update(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $categorie = $em->getRepository(Categorie::class)->find($id);
        
        if (!$categorie) {
            return $this->json(['error' => 'Catégorie introuvable'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['nom'])) $categorie->setNom($data['nom']);
        if (isset($data['description'])) $categorie->setDescription($data['description']);

        $em->flush();

        return $this->json(['message' => 'Catégorie mise à jour avec succès']);
    }

    #[Route('/{id}', name: 'categorie_delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em): JsonResponse
    {
        $categorie = $em->getRepository(Categorie::class)->find($id);
        
        if (!$categorie) {
            return $this->json(['error' => 'Catégorie introuvable'], 404);
        }

        if ($categorie->getProduits()->count() > 0) {
            return $this->json(['error' => 'Impossible de supprimer une catégorie qui contient des produits'], 409);
        }

        $em->remove($categorie);
        $em->flush();

        return $this->json(['message' => 'Catégorie supprimée avec succès']);
    }
}