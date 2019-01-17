import { Component, OnInit } from '@angular/core';
import { WebsiteService } from '../../services/website.service';

@Component({
  selector: 'app-website-list',
  templateUrl: './website-list.component.html',
  styleUrls: ['./website-list.component.scss']
})
export class WebsiteListComponent implements OnInit {

  private websites: [];

  constructor(private ws: WebsiteService) {
  }

  ngOnInit() {
    this.ws.get().subscribe((res: any) => {
      this.websites = res;
    });
  }

}
