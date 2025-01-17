import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { catchError, Observable } from 'rxjs';
import { Usuarios } from '../interfaces/usuarios';

@Injectable({
  providedIn: 'root'
})
export class UsuariosService {

  API_URL: string = 'https://gorest.co.in/public/v1/users';

  constructor(private http: HttpClient) {}

  getUsuarios(): Observable<Usuarios[]> {
    return this.http.get<Usuarios[]>(this.API_URL)
      .pipe(
        catchError((error: any) => {
          console.error('Error al obtener usuarios:', error);
          return ([]); // Retorna un array vacío en caso de error
        })
      );
  }
}
