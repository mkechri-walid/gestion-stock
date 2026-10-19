import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { CategorieService } from '../../../services/categorie.service';
import { Categorie } from '../../../models/categorie.model';

@Component({
  selector: 'app-categorie-list',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './categorie-list.html',
  styleUrls: ['./categorie-list.css']
})
export class CategorieListComponent implements OnInit {
  categories: Categorie[] = [];
  loading: boolean = true;

  constructor(private categorieService: CategorieService) {}

  ngOnInit() {
    this.loadCategories();
  }

  loadCategories() {
    this.loading = true;
    this.categorieService.getAll().subscribe({
      next: (data) => {
        this.categories = data;
        this.loading = false;
      },
      error: (err) => {
        console.error('Erreur lors du chargement', err);
        this.loading = false;
      }
    });
  }

  deleteCategorie(id: number) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')) {
      this.categorieService.delete(id).subscribe({
        next: () => {
          this.loadCategories();
        },
        error: (err) => {
          alert('Erreur lors de la suppression. La catégorie contient peut-être des produits.');
          console.error(err);
        }
      });
    }
  }
}