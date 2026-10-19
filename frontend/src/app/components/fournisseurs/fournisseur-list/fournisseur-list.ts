import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { FournisseurService } from '../../../services/fournisseur.service';
import { Fournisseur } from '../../../models/fournisseur.model';

@Component({
  selector: 'app-fournisseur-list',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './fournisseur-list.html',
  styleUrl: './fournisseur-list.css'
})
export class FournisseurListComponent implements OnInit {
  fournisseurs: Fournisseur[] = [];
  loading: boolean = true;

  constructor(private fournisseurService: FournisseurService) {}

  ngOnInit() {
    this.loadFournisseurs();
  }

  loadFournisseurs() {
    this.loading = true;
    this.fournisseurService.getAll().subscribe({
      next: (data) => {
        this.fournisseurs = data;
        this.loading = false;
      },
      error: (err) => {
        console.error('Erreur lors du chargement', err);
        this.loading = false;
      }
    });
  }

  deleteFournisseur(id: number) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce fournisseur ?')) {
      this.fournisseurService.delete(id).subscribe({
        next: () => {
          this.loadFournisseurs();
        },
        error: (err) => {
          alert('Erreur lors de la suppression');
          console.error(err);
        }
      });
    }
  }
}