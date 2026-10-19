import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { MouvementStock } from '../models/mouvement-stock.model';

@Injectable({
  providedIn: 'root'
})
export class MouvementStockService {
  private apiUrl = 'http://localhost:8000/api/mouvements';

  constructor(private http: HttpClient) { }

  getAll(): Observable<MouvementStock[]> {
    return this.http.get<MouvementStock[]>(this.apiUrl);
  }

  getByProduit(produitId: number): Observable<MouvementStock[]> {
    return this.http.get<MouvementStock[]>(`${this.apiUrl}/produit/${produitId}`);
  }

  create(mouvement: any): Observable<any> {
    return this.http.post(this.apiUrl, mouvement);
  }
}