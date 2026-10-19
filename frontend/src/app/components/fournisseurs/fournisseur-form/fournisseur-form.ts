import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router, ActivatedRoute, RouterModule } from '@angular/router';
import { FournisseurService } from '../../../services/fournisseur.service';
import { Fournisseur } from '../../../models/fournisseur.model';

@Component({
  selector: 'app-fournisseur-form',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule],
  templateUrl: './fournisseur-form.html',
  styleUrl: './fournisseur-form.css'
})
export class FournisseurFormComponent implements OnInit {
  fournisseur: Fournisseur = {
    id: 1,
    nom: '',
    email: '',
    telephone: '',
    adresse: '',
    pays: '',
    date_creation:new Date()

  };

  isEditMode: boolean = false;
  fournisseurId: number | null = null;
  loading: boolean = false;

  constructor(
    private fournisseurService: FournisseurService,
    private router: Router,
    private route: ActivatedRoute
  ) {}

  ngOnInit() {
    const id = this.route.snapshot.paramMap.get('id');
    if (id) {
      this.isEditMode = true;
      this.fournisseurId = +id;
      this.loadFournisseur(this.fournisseurId);
    }
  }

  loadFournisseur(id: number) {
    this.fournisseurService.getById(id).subscribe({
      next: (data) => this.fournisseur = data,
      error: (err) => console.error('Erreur chargement fournisseur', err)
    });
  }

  onSubmit() {
    this.loading = true;

    if (this.isEditMode && this.fournisseurId) {
      this.fournisseurService.update(this.fournisseurId, this.fournisseur).subscribe({
        next: () => {
          alert('Fournisseur modifié avec succès');
          this.router.navigate(['/fournisseurs']);
        },
        error: (err) => {
          alert('Erreur lors de la modification');
          console.error(err);
          this.loading = false;
        }
      });
    } else {
      this.fournisseurService.create(this.fournisseur).subscribe({
        next: () => {
          alert('Fournisseur créé avec succès');
          this.router.navigate(['/fournisseurs']);
        },
        error: (err) => {
          alert('Erreur lors de la création');
          console.error(err);
          this.loading = false;
        }
      });
    }
  }
}