import { Produit } from './produit.model';
import { User } from './user.model';
import { Commande } from './commande.model';

export type TypeMouvement = 'entree' | 'sortie';
export type MotifMouvement = 'achat' | 'vente' | 'ajustement' | 'retour';

export interface MouvementStock {
  id: number;
  produit_id: number;
  produit?: Produit;
  type: TypeMouvement;
  quantite: number;
  motif: MotifMouvement;
  date_mouvement: Date;
  utilisateur_id: number;
  utilisateur?: User;
  commande_id?: number;
  commande?: Commande;
  commentaire: string;
}