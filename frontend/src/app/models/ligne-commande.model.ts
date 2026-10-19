import { Commande } from './commande.model';
import { Produit } from './produit.model';

export interface LigneCommande {
  id: number;
  commande_id: number;
  commande?: Commande;
  produit_id: number;
  produit?: Produit;
  quantite: number;
  prix_unitaire: number;
  sous_total: number;
}