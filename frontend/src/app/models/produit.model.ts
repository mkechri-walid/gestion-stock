export interface Produit {
  id?: number;
  nom: string;
  reference: string;
  description?: string;
  prix_unitaire: number;
  quantite_stock: number;
  seuil_alerte: number;
  categorie?: {
    id: number;
    nom: string;
  };
  categorie_id?: number;
  fournisseur?: {
    id: number;
    nom: string;
  };
  fournisseur_id?: number;
  date_creation?: string;
  date_modification?: string;
  stock_bas?: boolean;
}
export interface ProduitRequest {
  nom: string;
  reference: string;
  description?: string;
  prix_unitaire: number;
  quantite_stock: number;
  seuil_alerte: number;
  categorie_id: number;
  fournisseur_id: number;
}