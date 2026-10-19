import { Routes } from '@angular/router';
import { LoginComponent } from './components/login/login';
import { DashboardComponent } from './components/dashboard/dashboard';
import { ProduitListComponent } from './components/produits/produit-list/produit-list';
import { ProduitFormComponent } from './components/produits/produit-form/produit-form';
import { ProduitDetailComponent } from './components/produits/produit-detail/produit-detail';
import { CategorieListComponent } from './components/categories/categorie-list/categorie-list';
import { CategorieFormComponent } from './components/categories/categorie-form/categorie-form';
import { FournisseurListComponent } from './components/fournisseurs/fournisseur-list/fournisseur-list';
import { FournisseurFormComponent } from './components/fournisseurs/fournisseur-form/fournisseur-form';
import { CommandeListComponent } from './components/commandes/commande-list/commande-list';
import { CommandeFormComponent } from './components/commandes/commande-form/commande-form';
import { CommandeDetailComponent } from './components/commandes/commande-detail/commande-detail';
import { AlerteListComponent } from './components/alertes/alerte-list/alerte-list';
import { MouvementListComponent } from './components/mouvements-stock/mouvement-list/mouvement-list';
import { MouvementFormComponent } from './components/mouvements-stock/mouvement-form/mouvement-form';

export const routes: Routes = [
  { path: '', redirectTo: '/login', pathMatch: 'full' },
  { path: 'login', component: LoginComponent },
  { path: 'dashboard', component: DashboardComponent },
  
  // Produits
  { path: 'produits', component: ProduitListComponent },
  { path: 'produits/new', component: ProduitFormComponent },
  { path: 'produits/:id', component: ProduitDetailComponent },
  { path: 'produits/:id/edit', component: ProduitFormComponent },
  
  // Catégories
  { path: 'categories', component: CategorieListComponent },
  { path: 'categories/new', component: CategorieFormComponent },
  { path: 'categories/:id/edit', component: CategorieFormComponent },
  
  // Fournisseurs
  { path: 'fournisseurs', component: FournisseurListComponent },
  { path: 'fournisseurs/new', component: FournisseurFormComponent },
  { path: 'fournisseurs/:id/edit', component: FournisseurFormComponent },
  
  // Commandes
  { path: 'commandes', component: CommandeListComponent },
  { path: 'commandes/new', component: CommandeFormComponent },
  { path: 'commandes/:id', component: CommandeDetailComponent },
  
  // Alertes
  { path: 'alertes', component: AlerteListComponent },
  
  // Mouvements
  { path: 'mouvements', component: MouvementListComponent },
  { path: 'mouvements/new', component: MouvementFormComponent },
  
  { path: '**', redirectTo: '/dashboard' }
];