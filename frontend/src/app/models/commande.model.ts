import { Fournisseur } from './fournisseur.model';
import { User } from './user.model';
import { LigneCommande } from './ligne-commande.model';

export type StatutCommande = 'en_attente' | 'validee' | 'livree' | 'annulee';

export interface Commande {
  id: number;
  numero_commande: string;
  fournisseur_id: number;
  fournisseur?: Fournisseur;
  date_commande: Date;
  date_livraison_prevue: Date;
  statut: StatutCommande;
  montant_total: number;
  utilisateur_id: number;
  utilisateur?: User;
  lignes?: LigneCommande[];
}