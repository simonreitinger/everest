import { Component, OnInit, ViewChild } from '@angular/core';
import { InstallationService } from '../services/installation.service';
import { InstallationModel } from '../models/installation.model';
import { ContaoManagerService } from '../services/contao-manager.service';
import { SoftwareService } from '../services/software.service';
import { SoftwareModel } from '../models/software.model';
import { MatDialog, MatPaginator, MatSort, MatTableDataSource } from '@angular/material';
import { MonitoringDialogComponent } from '../monitoring-dialog/monitoring-dialog.component';
import { InstallationAddComponent } from '../installation-add/installation-add.component';
import { ConfigService } from '../services/config.service';
import { $e } from 'codelyzer/angular/styles/chars';

const TABLE_OPTIONS = {
  displayedColumns: ['cleanUrl', 'software', 'softwareVersion', 'platform', 'platformVersion', 'status', 'detail']
};

@Component({
  selector: 'app-installation-list',
  templateUrl: './installation-list.component.html',
  styleUrls: ['./installation-list.component.scss']
})
export class InstallationListComponent implements OnInit {

  tableOptions = TABLE_OPTIONS;

  installations: InstallationModel[];
  softwares: SoftwareModel[];
  phpVersions: string[];

  @ViewChild(MatSort, { static: false }) sort: MatSort;
  dataSource: MatTableDataSource<InstallationModel>;

  @ViewChild(MatPaginator, { static: true }) paginator: MatPaginator;
  total = 0;
  perPage = 10;

  constructor(
    private dialog: MatDialog,
    private is: InstallationService,
    private cms: ContaoManagerService,
    private cs: ConfigService,
    private ss: SoftwareService
  ) {
  }

  ngOnInit() {
    this.is.getCount().subscribe((res: any) => {
      this.total = res.total;
      this.paginator.length = this.total;
    });

    this.is.getAll().subscribe((res: InstallationModel[]) => {
      this.installations = res;
      this.dataSource = new MatTableDataSource(this.installations);
      this.dataSource.paginator = this.paginator;
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
    this.dataSource.data = this.installations.sort((a, b) => {
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

  openMonitoringDialog(installation: InstallationModel) {
    this.dialog.open(MonitoringDialogComponent, {
      data: {
        installation: installation
      },
      height: '450px',
      width: '800px'
    });
  }

  openDialog() {
    const dialogRef = this.dialog.open(InstallationAddComponent, {
      width: '500px',
      height: '200px'
    });
  }

  update(installation: InstallationModel) {
    this.cs.forceUpdate(installation).subscribe((res: any) => {
        console.log(res);
    });
  }

  delete(installation: InstallationModel) {
    this.is.remove(installation.hash).subscribe((res: any) => {
      if (res.success) {
        this.installations = this.installations.filter(i => i.hash !== installation.hash);
        this.dataSource.data = this.installations;
      }
    });
  }
}
