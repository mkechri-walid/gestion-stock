export interface DashboardStats {
  totalProduits: number;
  produitsStockBas: number;
  totalCommandes: number;
  commandesEnAttente: number;
  totalMouvements: number;
  alertesNonLues: number;
  chiffreAffaire: number;
  topProduits: TopProduit[];
}

export interface TopProduit {
  id: number;
  nom: string;
  reference: string;
  quantiteVendue: number;
  chiffreAffaire: number;
}