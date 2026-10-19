import { Produit } from './produit.model';

export type TypeAlerte = 'stock_bas' | 'rupture';

export interface Alerte {
  id: number;
  produit_id: number;
  produit?: Produit;
  type: TypeAlerte;
  message: string;
  date_alerte: Date;
  lu: boolean;
}