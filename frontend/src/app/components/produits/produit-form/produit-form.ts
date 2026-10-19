import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router, ActivatedRoute, RouterModule } from '@angular/router';
import { ProduitService } from '../../../services/produit.service';
import { CategorieService } from '../../../services/categorie.service';
import { FournisseurService } from '../../../services/fournisseur.service';
import { Produit } from '../../../models/produit.model';
import { Categorie } from '../../../models/categorie.model';
import { Fournisseur } from '../../../models/fournisseur.model';

@Component({
  selector: 'app-produit-form',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule],
  templateUrl: './produit-form.html',
  styleUrl: './produit-form.css'
})
export class ProduitFormComponent implements OnInit {
  produit: Produit = {
    nom: '',
    reference: '',
    description: '',
    prix_unitaire: 0,
    quantite_stock: 0,
    seuil_alerte: 10,
    categorie_id: 0,
    fournisseur_id: 0
  };

  categories: Categorie[] = [];
  fournisseurs: Fournisseur[] = [];
  isEditMode: boolean = false;
  produitId: number | null = null;
  loading: boolean = false;

  constructor(
    private produitService: ProduitService,
    private categorieService: CategorieService,
    private fournisseurService: FournisseurService,
    private router: Router,
    private route: ActivatedRoute
  ) {}

  ngOnInit() {
    this.loadCategories();
    this.loadFournisseurs();
    
    const id = this.route.snapshot.paramMap.get('id');
    if (id) {
      this.isEditMode = true;
      this.produitId = +id;
      this.loadProduit(this.produitId);
    }
  }

  loadCategories() {
    this.categorieService.getAll().subscribe({
      next: (data) => this.categories = data,
      error: (err) => console.error('Erreur chargement catégories', err)
    });
  }

  loadFournisseurs() {
    this.fournisseurService.getAll().subscribe({
      next: (data) => this.fournisseurs = data,
      error: (err) => console.error('Erreur chargement fournisseurs', err)
    });
  }

  loadProduit(id: number) {
    this.produitService.getProduit(id).subscribe({
      next: (data) => {
        this.produit = data;
        this.produit.categorie_id = data.categorie_id;
        this.produit.fournisseur_id = data.fournisseur_id;
      },
      error: (err) => console.error('Erreur chargement produit', err)
    });
  }
onSubmit() {
  this.loading = true;

  const produitData = {
    nom: this.produit.nom,
    reference: this.produit.reference,
    description: this.produit.description,
    prix_unitaire: this.produit.prix_unitaire,
    quantite_stock: this.produit.quantite_stock,
    seuil_alerte: this.produit.seuil_alerte,
    categorie_id: Number(this.produit.categorie_id), // Conversion en nombre pour Symfony
    fournisseur_id: Number(this.produit.fournisseur_id)
  };

  this.produitService.createProduit(produitData).subscribe({
    next: (response) => {
      console.log('Réponse serveur:', response);
      alert('Produit RÉELLEMENT enregistré en base de données !');
      this.router.navigate(['/produits']);
    },
    error: (err) => {
      this.loading = false;
      console.error('Erreur détaillée:', err);
      // Affiche l'erreur réelle renvoyée par Symfony
      alert('Erreur serveur : ' + (err.error?.error || 'Problème de connexion'));
    }
  });
}
}