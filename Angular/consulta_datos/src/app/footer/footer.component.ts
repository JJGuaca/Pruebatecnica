import { Component,HostListener, OnInit } from '@angular/core';

@Component({
  selector: 'app-footer',
  standalone: true,
  imports: [],
  templateUrl: './footer.component.html',
  styleUrl: './footer.component.scss'
})
export class FooterComponent implements OnInit {
  ngOnInit(): void {
    this.doSomethingOnWindowsScroll();
  }
  verticalOffset:any=0
  @HostListener("window:scroll", [])
  doSomethingOnWindowsScroll(){
    this.verticalOffset = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
     if(this.verticalOffset > 280){      
      document.getElementById('volverArriba')?.classList.remove('d-none');
     }else{
      document.getElementById('volverArriba')?.classList.add('d-none');
     }
  }
  volverArriba(){
    window.pageYOffset = 0;
    document.documentElement.scrollTop = 0
    document.body.scrollTop = 0;
  }

  
}
