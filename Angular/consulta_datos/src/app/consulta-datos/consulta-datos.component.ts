import { Component } from '@angular/core';
import { UsuariosService } from '../services/usuarios.service';
import { Usuarios } from '../interfaces/usuarios';
import { CommonModule } from '@angular/common'; 


@Component({
  selector: 'app-consulta-datos',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './consulta-datos.component.html',
  styleUrl: './consulta-datos.component.scss'
})
export class ConsultaDatosComponent {
  usuariosLista: Usuarios[]=[];
  data = [];
  meta = [];
  
  API_BASE_URL:string='https://gorest.co.in/public/v1/users';
  constructor(private usuariosService:UsuariosService){}
  

  ngOnInit(): void {
    this.getUsuarios();
    
    // Convirtiendo a un arreglo de pares clave-valor
    const entradas = Object.entries(this.usuariosLista);

    // Iterando sobre el arreglo
    entradas.forEach(([clave, valor]) => {
      console.log(clave, valor);
    });

    // Utilizando Lodash
    // const valores = values(this.usuariosLista);
    // console.log(valores);
  }
  getUsuarios(){
    this.usuariosService.getUsuarios().subscribe({
      next:(result) =>{
        this.usuariosLista = result;
      },
      error: (error) =>{
        console.log(error);
      }
      
    })
  }
  
}
