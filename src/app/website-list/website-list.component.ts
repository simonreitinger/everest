import { Component, OnInit } from '@angular/core';
import { WebsiteService } from '../services/website.service';
import { WebsiteModel } from '../models/website.model';
import { ContaoManagerService } from '../services/contao-manager.service';
import { SoftwareService } from '../services/software.service';
import { SoftwareModel } from '../models/software.model';

@Component({
  selector: 'app-website-list',
  templateUrl: './website-list.component.html',
  styleUrls: ['./website-list.component.scss']
})
export class WebsiteListComponent implements OnInit {

  websites: WebsiteModel[];
  softwares: SoftwareModel[];
  phpVersions: string[];

  constructor(
    private ws: WebsiteService,
    private cms: ContaoManagerService,
    private ss: SoftwareService
  ) {
  }

  ngOnInit() {
    this.ws.getAll().subscribe((res: WebsiteModel[]) => {
      this.websites = res;
    });
    this.ss.getAll().subscribe((res: SoftwareModel[]) => {
      this.softwares = res;
      for (const software of this.softwares) {
        if (software.name === 'php') {
          this.phpVersions = software.versions;
          console.log(this.phpVersions);
          break;
        }
      }
    });
  }


}
