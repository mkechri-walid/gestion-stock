import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { MouvementStockService } from '../../../services/mouvement-stock.service';
import { MouvementStock } from '../../../models/mouvement-stock.model';

@Component({
  selector: 'app-mouvement-list',
  standalone: true,
  imports: [CommonModule, RouterModule, FormsModule],
  templateUrl: './mouvement-list.html',
  styleUrl: './mouvement-list.css'
})
export class MouvementListComponent implements OnInit {
  mouvements: MouvementStock[] = [];
  loading: boolean = true;
  filterType: string = '';

  constructor(private mouvementService: MouvementStockService) {}

  ngOnInit() {
    this.loadMouvements();
  }

  loadMouvements() {
    this.loading = true;
    this.mouvementService.getAll().subscribe({
      next: (data) => {
        this.mouvements = data;
        this.loading = false;
      },
      error: (err) => {
        console.error('Erreur lors du chargement', err);
        this.loading = false;
      }
    });
  }

  get filteredMouvements() {
    if (!this.filterType) {
      return this.mouvements;
    }
    return this.mouvements.filter(m => m.type === this.filterType);
  }

  getTypeClass(type: string): string {
    return type === 'entree' ? 'type-entree' : 'type-sortie';
  }

  getTypeIcon(type: string): string {
    return type === 'entree' ? '📥' : '📤';
  }

  getTypeLabel(type: string): string {
    return type === 'entree' ? 'Entrée' : 'Sortie';
  }
}