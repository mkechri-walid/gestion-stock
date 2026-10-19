import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';
import { MouvementStockService } from '../../../services/mouvement-stock.service';
import { ProduitService } from '../../../services/produit.service';
import { Produit } from '../../../models/produit.model';

@Component({
  selector: 'app-mouvement-form',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule],
  templateUrl: './mouvement-form.html',
  styleUrl: './mouvement-form.css'
})
export class MouvementFormComponent implements OnInit {
  mouvement = {
    produit_id: null as number | null,
    type: 'entree',
    quantite: 1,
    motif: 'achat',
    commentaire: ''
  };

  produits: Produit[] = [];
  loading: boolean = false;

  motifsEntree = ['achat', 'retour', 'ajustement'];
  motifsSortie = ['vente', 'perte', 'ajustement'];

  constructor(
    private mouvementService: MouvementStockService,
    private produitService: ProduitService,
    private router: Router
  ) {}

  ngOnInit() {
    this.loadProduits();
  }

  loadProduits() {
    this.produitService.getProduits().subscribe({
      next: (data) => this.produits = data,
      error: (err) => console.error('Erreur chargement produits', err)
    });
  }

  get motifsDisponibles() {
    return this.mouvement.type === 'entree' ? this.motifsEntree : this.motifsSortie;
  }

  onTypeChange() {
    // Réinitialiser le motif quand le type change
    this.mouvement.motif = this.motifsDisponibles[0];
  }

  onSubmit() {
    if (!this.mouvement.produit_id) {
      alert('Veuillez sélectionner un produit');
      return;
    }

    this.loading = true;

    this.mouvementService.create(this.mouvement).subscribe({
      next: (response) => {
        alert('Mouvement enregistré avec succès');
        this.router.navigate(['/mouvements']);
      },
      error: (err) => {
        alert('Erreur lors de l\'enregistrement');
        console.error(err);
        this.loading = false;
      }
    });
  }
}