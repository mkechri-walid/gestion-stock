import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Alerte } from '../models/alerte.model';

@Injectable({
  providedIn: 'root'
})
export class AlerteService {
  private apiUrl = 'http://localhost:8000/api/alertes';

  constructor(private http: HttpClient) { }

  getAll(): Observable<Alerte[]> {
    return this.http.get<Alerte[]>(this.apiUrl);
  }

  getNonLues(): Observable<Alerte[]> {
    return this.http.get<Alerte[]>(`${this.apiUrl}?non_lues=true`);
  }

  generer(): Observable<any> {
    return this.http.post(`${this.apiUrl}/generer`, {});
  }

  marquerLue(id: number): Observable<any> {
    return this.http.patch(`${this.apiUrl}/${id}/lire`, {});
  }

  toutLire(): Observable<any> {
    return this.http.patch(`${this.apiUrl}/tout-lire`, {});
  }

  delete(id: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}/${id}`);
  }
}