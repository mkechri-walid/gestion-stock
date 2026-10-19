import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, Router, RouterModule } from '@angular/router';
import { ProduitService } from '../../../services/produit.service';
import { Produit } from '../../../models/produit.model';

@Component({
  selector: 'app-produit-detail',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './produit-detail.html',
  styleUrl: './produit-detail.css'
})
export class ProduitDetailComponent implements OnInit {
  produit: Produit | null = null;
  loading: boolean = true;

  constructor(
    private produitService: ProduitService,
    private route: ActivatedRoute,
    private router: Router
  ) {}

  ngOnInit() {
    const id = this.route.snapshot.paramMap.get('id');
    if (id) {
      this.loadProduit(+id);
    }
  }

  loadProduit(id: number) {
    this.produitService.getProduit(id).subscribe({
      next: (data) => {
        this.produit = data;
        this.loading = false;
      },
      error: (err) => {
        console.error('Erreur lors du chargement', err);
        this.loading = false;
      }
    });
  }

  deleteProduit() {
    if (this.produit && confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')) {
      this.produitService.deleteProduit(this.produit.id!).subscribe({
        next: () => {
          alert('Produit supprimé avec succès');
          this.router.navigate(['/produits']);
        },
        error: (err) => {
          alert('Erreur lors de la suppression');
          console.error(err);
        }
      });
    }
  }
}