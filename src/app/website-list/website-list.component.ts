import { Component, OnInit, ViewChild } from '@angular/core';
import { WebsiteService } from '../services/website.service';
import { WebsiteModel } from '../models/website.model';
import { ContaoManagerService } from '../services/contao-manager.service';
import { SoftwareService } from '../services/software.service';
import { SoftwareModel } from '../models/software.model';
import { WebsiteAddComponent } from '../website-add/website-add.component';
import { MatDialog, MatSort, MatTableDataSource } from '@angular/material';
import { MonitoringComponent } from '../monitoring/monitoring.component';

const TABLE_OPTIONS = {
  displayedColumns: ['cleanUrl', 'software', 'softwareVersion', 'platform', 'platformVersion', 'status', 'detail']
};

@Component({
  selector: 'app-website-list',
  templateUrl: './website-list.component.html',
  styleUrls: ['./website-list.component.scss']
})
export class WebsiteListComponent implements OnInit {

  tableOptions = TABLE_OPTIONS;

  websites: WebsiteModel[];
  softwares: SoftwareModel[];
  phpVersions: string[];

  @ViewChild(MatSort) sort: MatSort;
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
      console.log(res);
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

  sortData(event) {
    this.dataSource.data = this.websites.sort((a, b) => {
      switch (event.direction) {
        case 'asc':
        default:
          return a[event.active] > b[event.active] ? 1 : -1;
        case 'desc':
          return a[event.active] < b[event.active] ? 1 : -1;
      }
      return 0;
    });
  }

  openMonitoringDialog(website: WebsiteModel) {
    this.dialog.open(MonitoringComponent, {
      data: {
        website: website
      },
      height: '450px',
      width: '800px'
    });
  }

  openDialog() {
    const dialogRef = this.dialog.open(WebsiteAddComponent, {
      width: '500px',
      height: '200px'
    });
  }


}
