export interface User {
  id: number;
  nom: string;
  prenom: string;
  email: string;
  role: 'admin' | 'gestionnaire' | 'utilisateur';
  date_creation: Date;
  actif: boolean;
}