import { Component, OnInit } from '@angular/core';
import { WebsiteService } from '../services/website.service';
import { WebsiteModel } from '../models/website.model';
import { ContaoManagerService } from '../services/contao-manager.service';

@Component({
  selector: 'app-website-list',
  templateUrl: './website-list.component.html',
  styleUrls: ['./website-list.component.scss']
})
export class WebsiteListComponent implements OnInit {

  websites: WebsiteModel[];

  constructor(
    private ws: WebsiteService,
    private cms: ContaoManagerService) {
  }

  ngOnInit() {
    this.ws.getAll().subscribe((res: any) => {
      this.websites = res;
    });
  }


}
