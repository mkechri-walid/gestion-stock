import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, Router, RouterModule } from '@angular/router';
import { CommandeService } from '../../../services/commande.service';

@Component({
  selector: 'app-commande-detail',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './commande-detail.html',
  styleUrl: './commande-detail.css'
})
export class CommandeDetailComponent implements OnInit {
  commande: any = null;
  loading: boolean = true;

  constructor(
    private commandeService: CommandeService,
    private route: ActivatedRoute,
    private router: Router
  ) {}

  ngOnInit() {
    const id = this.route.snapshot.paramMap.get('id');
    if (id) {
      this.loadCommande(+id);
    }
  }

  loadCommande(id: number) {
    this.commandeService.getById(id).subscribe({
      next: (data) => {
        this.commande = data;
        this.loading = false;
      },
      error: (err) => {
        console.error('Erreur lors du chargement', err);
        this.loading = false;
      }
    });
  }

  changerStatut(nouveauStatut: string) {
    if (confirm(`Changer le statut vers "${nouveauStatut}" ?`)) {
      this.commandeService.updateStatut(this.commande.id, nouveauStatut).subscribe({
        next: () => {
          alert('Statut mis à jour avec succès');
          this.loadCommande(this.commande.id);
        },
        error: (err) => {
          alert('Erreur lors de la mise à jour');
          console.error(err);
        }
      });
    }
  }

  deleteCommande() {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette commande ?')) {
      this.commandeService.delete(this.commande.id).subscribe({
        next: () => {
          alert('Commande supprimée avec succès');
          this.router.navigate(['/commandes']);
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
}