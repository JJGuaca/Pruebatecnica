import { NgFor, NgIf } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { RouterLink } from '@angular/router';


@Component({
  selector: 'app-header',
  standalone: true,
  imports: [RouterLink,NgFor,NgIf ],
  templateUrl: './header.component.html',
  styleUrl: './header.component.scss'
})
export class HeaderComponent implements OnInit {
  
  breadcrumb:any[]=[];

  items:any[]=[
    {name:'consulta',path:'/',active:'active'},
  ]
  setActive(item:any){
    for(let i =0; i< this.items.length;i++){
      this.items[i].active='';
    }
    this.items[item].active ='active';
    
    if(this.breadcrumb.includes(this.items[item]) == false){
      this.breadcrumb.push(this.items[item]);
    }
    this.navbreadcrumb();
  
  }

  setActiveSubMenu(submenu:any,id:any){
    for(let i=0; i< this.items[submenu].submenu.length; i++){
      this.items[submenu].submenu[i].active='';
    }
    this.items[submenu].submenu[id].active='active';

    if(this.breadcrumb.includes(this.items[submenu].submenu[id]) == false){
      this.breadcrumb.push(this.items[submenu].submenu[id]);      
    }   
    this.navbreadcrumb(); 
    
  }
  ngOnInit(): void {
    for(let i=0; i< this.items.length; i++){
      this.items[i].active = ''
    }

    for(let i=0; i< this.items.length; i++){
      if(this.items[i].path === window.location.pathname){
        this.items[i].active = 'active';
      }
    }
    this.navbreadcrumb();

  }
  navbreadcrumb(){
    for(let i=0; i < (this.breadcrumb.length -1 ); i++){
      this.breadcrumb[i].active ='';
    }
  }
  Nnavbreadcrumb(valor:any){
    console.log(valor, this.breadcrumb.length);
    for(let i= valor; i< this.breadcrumb.length; i++){
      this.breadcrumb[i].active='active';
    }
    this.breadcrumb.pop();
  }
}
