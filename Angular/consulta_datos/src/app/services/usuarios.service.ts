import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { catchError, Observable } from 'rxjs';
import { Datos,Usuarios } from '../interfaces/usuarios';

@Injectable({
  providedIn: 'root'
})
export class UsuariosService {

  API_URL: string = 'https://gorest.co.in/public/v1/users';

  constructor(private http: HttpClient) {}

  getUsuarios(): Observable<Datos[]> {
    return this.http.get<Datos[]>(this.API_URL)
      .pipe(
        catchError((error: any) => {
          console.error('Error al obtener usuarios:', error);
          return ([]); // Retorna un array vacío en caso de error
        })
      );
  }
  getRegistros(valor:any):Observable<Usuarios[]>{
    return this.http.get<Usuarios[]>(valor)
      .pipe(
        catchError((error: any) => {
          console.error('Error al obtener registros:', error);
          return ([]); // Retorna un array vacío en caso de error
        })
      ); 
  }
}
