<?php

namespace App\Controller;

use App\Entity\Fournisseur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/fournisseurs')]
class FournisseurController extends AbstractController
{
    #[Route('', name: 'fournisseurs_list', methods: ['GET'])]
    public function index(EntityManagerInterface $em): JsonResponse
    {
        $fournisseurs = $em->getRepository(Fournisseur::class)->findAll();
        
        $data = array_map(function($fournisseur) {
            return [
                'id' => $fournisseur->getId(),
                'nom' => $fournisseur->getNom(),
                'email' => $fournisseur->getEmail(),
                'telephone' => $fournisseur->getTelephone(),
                'adresse' => $fournisseur->getAdresse(),
                'pays' => $fournisseur->getPays(),
                'date_creation' => $fournisseur->getDateCreation()?->format('Y-m-d H:i:s')
            ];
        }, $fournisseurs);

        return $this->json($data);
    }

    #[Route('/{id}', name: 'fournisseur_show', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $em): JsonResponse
    {
        $fournisseur = $em->getRepository(Fournisseur::class)->find($id);
        
        if (!$fournisseur) {
            return $this->json(['error' => 'Fournisseur introuvable'], 404);
        }

        return $this->json([
            'id' => $fournisseur->getId(),
            'nom' => $fournisseur->getNom(),
            'email' => $fournisseur->getEmail(),
            'telephone' => $fournisseur->getTelephone(),
            'adresse' => $fournisseur->getAdresse(),
            'pays' => $fournisseur->getPays(),
            'date_creation' => $fournisseur->getDateCreation()?->format('Y-m-d H:i:s')
        ]);
    }

    #[Route('', name: 'fournisseur_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validation
        if (!isset($data['nom']) || !isset($data['email'])) {
            return $this->json(['error' => 'Le nom et l\'email sont requis'], 400);
        }

        // Vérifier si l'email existe déjà
        $existingFournisseur = $em->getRepository(Fournisseur::class)->findOneBy(['email' => $data['email']]);
        if ($existingFournisseur) {
            return $this->json(['error' => 'Cet email est déjà utilisé'], 409);
        }

        // Créer le fournisseur
        $fournisseur = new Fournisseur();
        $fournisseur->setNom($data['nom']);
        $fournisseur->setEmail($data['email']);
        $fournisseur->setTelephone($data['telephone'] ?? null);
        $fournisseur->setAdresse($data['adresse'] ?? null);
        $fournisseur->setPays($data['pays'] ?? null);

        $em->persist($fournisseur);
        $em->flush();

        return $this->json([
            'message' => 'Fournisseur créé avec succès',
            'id' => $fournisseur->getId()
        ], 201);
    }

    #[Route('/{id}', name: 'fournisseur_update', methods: ['PUT'])]
    public function update(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $fournisseur = $em->getRepository(Fournisseur::class)->find($id);
        
        if (!$fournisseur) {
            return $this->json(['error' => 'Fournisseur introuvable'], 404);
        }

        $data = json_decode($request->getContent(), true);

        // Vérifier si l'email est déjà utilisé par un autre fournisseur
        if (isset($data['email']) && $data['email'] !== $fournisseur->getEmail()) {
            $existingFournisseur = $em->getRepository(Fournisseur::class)->findOneBy(['email' => $data['email']]);
            if ($existingFournisseur) {
                return $this->json(['error' => 'Cet email est déjà utilisé'], 409);
            }
        }

        // Mettre à jour les champs
        if (isset($data['nom'])) $fournisseur->setNom($data['nom']);
        if (isset($data['email'])) $fournisseur->setEmail($data['email']);
        if (isset($data['telephone'])) $fournisseur->setTelephone($data['telephone']);
        if (isset($data['adresse'])) $fournisseur->setAdresse($data['adresse']);
        if (isset($data['pays'])) $fournisseur->setPays($data['pays']);

        $em->flush();

        return $this->json(['message' => 'Fournisseur mis à jour avec succès']);
    }

    #[Route('/{id}', name: 'fournisseur_delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em): JsonResponse
    {
        $fournisseur = $em->getRepository(Fournisseur::class)->find($id);
        
        if (!$fournisseur) {
            return $this->json(['error' => 'Fournisseur introuvable'], 404);
        }

        $em->remove($fournisseur);
        $em->flush();

        return $this->json(['message' => 'Fournisseur supprimé avec succès']);
    }
}