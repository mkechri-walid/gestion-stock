import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';
import { CommandeService } from '../../../services/commande.service';
import { FournisseurService } from '../../../services/fournisseur.service';
import { ProduitService } from '../../../services/produit.service';
import { Fournisseur } from '../../../models/fournisseur.model';
import { Produit } from '../../../models/produit.model';

@Component({
  selector: 'app-commande-form',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule],
  templateUrl: './commande-form.html',
  styleUrl: './commande-form.css'
})
export class CommandeFormComponent implements OnInit {
  fournisseurId: number | null = null;
  dateLivraisonPrevue: string = '';
  
  fournisseurs: Fournisseur[] = [];
  produits: Produit[] = [];
  
  lignes: Array<{produit_id: number | null, quantite: number}> = [];
  
  loading: boolean = false;

  constructor(
    private commandeService: CommandeService,
    private fournisseurService: FournisseurService,
    private produitService: ProduitService,
    private router: Router
  ) {}

  ngOnInit() {
    this.loadFournisseurs();
    this.loadProduits();
    this.ajouterLigne();
  }

  loadFournisseurs() {
    this.fournisseurService.getAll().subscribe({
      next: (data) => this.fournisseurs = data,
      error: (err) => console.error('Erreur chargement fournisseurs', err)
    });
  }

  loadProduits() {
    this.produitService.getProduits().subscribe({
      next: (data) => this.produits = data,
      error: (err) => console.error('Erreur chargement produits', err)
    });
  }

  ajouterLigne() {
    this.lignes.push({ produit_id: null, quantite: 1 });
  }

  supprimerLigne(index: number) {
    this.lignes.splice(index, 1);
  }

  onSubmit() {
    if (!this.fournisseurId) {
      alert('Veuillez sélectionner un fournisseur');
      return;
    }

    if (this.lignes.length === 0 || !this.lignes.some(l => l.produit_id)) {
      alert('Veuillez ajouter au moins un produit');
      return;
    }

    this.loading = true;

    const commandeData = {
      fournisseur_id: this.fournisseurId,
      date_livraison_prevue: this.dateLivraisonPrevue || null,
      produits: this.lignes.filter(l => l.produit_id).map(l => ({
        produit_id: l.produit_id,
        quantite: l.quantite
      }))
    };

    this.commandeService.create(commandeData).subscribe({
      next: () => {
        alert('Commande créée avec succès');
        this.router.navigate(['/commandes']);
      },
      error: (err) => {
        alert('Erreur lors de la création');
        console.error(err);
        this.loading = false;
      }
    });
  }
}