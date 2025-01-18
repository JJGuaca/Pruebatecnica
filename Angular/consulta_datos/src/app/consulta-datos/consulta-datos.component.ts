import { Component } from '@angular/core';
import { UsuariosService } from '../services/usuarios.service';
import { Usuarios,Datos } from '../interfaces/usuarios';
import { CommonModule } from '@angular/common'; 

@Component({
  selector: 'app-consulta-datos',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './consulta-datos.component.html',
  styleUrl: './consulta-datos.component.scss'
})
export class ConsultaDatosComponent {
  API_BASE_URL:string='https://gorest.co.in/public/v1/users';
  usuariosLista: Usuarios[]=[];
  NusuariosLista: Usuarios[]=[];
  dataLista: Datos[]=[];
  
  
  constructor(private usuariosService:UsuariosService){}
  

  ngOnInit(): void {
    this.getUsuarios();
  }
  getUsuarios(){
    this.usuariosService.getUsuarios().subscribe({
      next:(result) =>{
        this.dataLista = result;
        const entradas = Object.entries(this.dataLista);  
        const data =entradas[1];
        const registros = data[1];
        for(let i =0; i<registros['length'];i++){
          this.usuariosLista.push(registros[i]); 
          this.NusuariosLista.push(this.usuariosLista[i]);
        }        
      },
      error: (error) =>{
        console.log(error);
      }
      
    })
  }
  
}
