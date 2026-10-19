import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { AlerteService } from '../../../services/alerte.service';
import { Alerte } from '../../../models/alerte.model';

@Component({
  selector: 'app-alerte-list',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './alerte-list.html',
  styleUrl: './alerte-list.css'
})
export class AlerteListComponent implements OnInit {
  alertes: Alerte[] = [];
  loading: boolean = true;
  showOnlyUnread: boolean = true;

  constructor(private alerteService: AlerteService) {}

  ngOnInit() {
    this.loadAlertes();
  }

  loadAlertes() {
    this.loading = true;
    
    if (this.showOnlyUnread) {
      this.alerteService.getNonLues().subscribe({
        next: (data) => {
          this.alertes = data;
          this.loading = false;
        },
        error: (err) => {
          console.error('Erreur lors du chargement', err);
          this.loading = false;
        }
      });
    } else {
      this.alerteService.getAll().subscribe({
        next: (data) => {
          this.alertes = data;
          this.loading = false;
        },
        error: (err) => {
          console.error('Erreur lors du chargement', err);
          this.loading = false;
        }
      });
    }
  }

  genererAlertes() {
    if (confirm('Générer les alertes pour tous les produits en stock bas ?')) {
      this.alerteService.generer().subscribe({
        next: (response) => {
          alert(response.message || 'Alertes générées avec succès');
          this.loadAlertes();
        },
        error: (err) => {
          alert('Erreur lors de la génération des alertes');
          console.error(err);
        }
      });
    }
  }

  marquerLue(id: number) {
    this.alerteService.marquerLue(id).subscribe({
      next: () => {
        this.loadAlertes();
      },
      error: (err) => {
        console.error('Erreur', err);
      }
    });
  }

  toutLire() {
    if (confirm('Marquer toutes les alertes comme lues ?')) {
      this.alerteService.toutLire().subscribe({
        next: (response) => {
          alert(response.message || 'Toutes les alertes ont été marquées comme lues');
          this.loadAlertes();
        },
        error: (err) => {
          alert('Erreur');
          console.error(err);
        }
      });
    }
  }

  deleteAlerte(id: number) {
    if (confirm('Supprimer cette alerte ?')) {
      this.alerteService.delete(id).subscribe({
        next: () => {
          this.loadAlertes();
        },
        error: (err) => {
          console.error('Erreur', err);
        }
      });
    }
  }

  toggleFilter() {
    this.showOnlyUnread = !this.showOnlyUnread;
    this.loadAlertes();
  }

  getTypeClass(type: string): string {
    switch(type) {
      case 'stock_bas': return 'type-warning';
      case 'rupture': return 'type-danger';
      default: return '';
    }
  }

  getTypeIcon(type: string): string {
    switch(type) {
      case 'stock_bas': return '⚠️';
      case 'rupture': return '❌';
      default: return '🔔';
    }
  }
}