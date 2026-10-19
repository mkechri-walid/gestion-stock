import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router, ActivatedRoute, RouterModule } from '@angular/router';
import { CategorieService } from '../../../services/categorie.service';
import { Categorie } from '../../../models/categorie.model';

@Component({
  selector: 'app-categorie-form',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule],
  templateUrl: './categorie-form.html',
  styleUrl: './categorie-form.css'
})
export class CategorieFormComponent implements OnInit {
  categorie: Categorie = {
    id: 1,
    nom: '',
    description: ''
  };

  isEditMode: boolean = false;
  categorieId: number | null = null;
  loading: boolean = false;

  constructor(
    private categorieService: CategorieService,
    private router: Router,
    private route: ActivatedRoute
  ) {}

  ngOnInit() {
    const id = this.route.snapshot.paramMap.get('id');
    if (id) {
      this.isEditMode = true;
      this.categorieId = +id;
      this.loadCategorie(this.categorieId);
    }
  }

  loadCategorie(id: number) {
    this.categorieService.getById(id).subscribe({
      next: (data) => this.categorie = data,
      error: (err) => console.error('Erreur chargement catégorie', err)
    });
  }

  onSubmit() {
    this.loading = true;

    if (this.isEditMode && this.categorieId) {
      this.categorieService.update(this.categorieId, this.categorie).subscribe({
        next: () => {
          alert('Catégorie modifiée avec succès');
          this.router.navigate(['/categories']);
        },
        error: (err) => {
          alert('Erreur lors de la modification');
          console.error(err);
          this.loading = false;
        }
      });
    } else {
      this.categorieService.create(this.categorie).subscribe({
        next: () => {
          alert('Catégorie créée avec succès');
          this.router.navigate(['/categories']);
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