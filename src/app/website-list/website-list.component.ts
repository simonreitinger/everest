import { Component, OnInit } from '@angular/core';
import { WebsiteService } from '../services/website.service';
import { WebsiteModel } from '../models/website.model';
import { ContaoManagerService } from '../services/contao-manager.service';
import { SoftwareService } from '../services/software.service';
import { SoftwareModel } from '../models/software.model';
import { WebsiteAddComponent } from '../website-add/website-add.component';
import { MatDialog, MatTableDataSource } from '@angular/material';

@Component({
  selector: 'app-website-list',
  templateUrl: './website-list.component.html',
  styleUrls: ['./website-list.component.scss']
})
export class WebsiteListComponent implements OnInit {

  websites: WebsiteModel[];
  softwares: SoftwareModel[];
  phpVersions: string[];
  dataSource: MatTableDataSource<WebsiteModel>;

  constructor(
    private dialog: MatDialog,
    private ws: WebsiteService,
    private cms: ContaoManagerService,
    private ss: SoftwareService
  ) {
  }

  ngOnInit() {
    this.ws.getAll().subscribe((res: WebsiteModel[]) => {
      this.websites = res;
      this.dataSource = new MatTableDataSource(this.websites);
    });
    this.ss.getAll().subscribe((res: SoftwareModel[]) => {
      this.softwares = res;
      for (const software of this.softwares) {
        if (software.name === 'php') {
          this.phpVersions = software.versions;
          break;
        }
      }
    });
  }

  openDialog() {
    const dialogRef = this.dialog.open(WebsiteAddComponent, {
      width: '500px',
      height: '200px'
    });
  }


}
