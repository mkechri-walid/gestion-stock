import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { CommandeService } from '../../../services/commande.service';
import { Commande } from '../../../models/commande.model';

@Component({
  selector: 'app-commande-list',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './commande-list.html',
  styleUrl: './commande-list.css'
})
export class CommandeListComponent implements OnInit {
  commandes: Commande[] = [];
  loading: boolean = true;

  constructor(private commandeService: CommandeService) {}

  ngOnInit() {
    this.loadCommandes();
  }

  loadCommandes() {
    this.loading = true;
    this.commandeService.getAll().subscribe({
      next: (data) => {
        this.commandes = data;
        this.loading = false;
      },
      error: (err) => {
        console.error('Erreur lors du chargement', err);
        this.loading = false;
      }
    });
  }

  deleteCommande(id: number) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette commande ?')) {
      this.commandeService.delete(id).subscribe({
        next: () => {
          this.loadCommandes();
        },
        error: (err) => {
          alert('Erreur lors de la suppression');
          console.error(err);
        }
      });
    }
  }

  getStatusClass(statut: string): string {
    switch(statut) {
      case 'en_attente': return 'status-pending';
      case 'validee': return 'status-validated';
      case 'livree': return 'status-delivered';
      case 'annulee': return 'status-cancelled';
      default: return '';
    }
  }

  getStatusLabel(statut: string): string {
    switch(statut) {
      case 'en_attente': return '⏳ En Attente';
      case 'validee': return '✅ Validée';
      case 'livree': return '📦 Livrée';
      case 'annulee': return '❌ Annulée';
      default: return statut;
    }
  }
}