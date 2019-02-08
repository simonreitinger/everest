import { Component, OnInit, ViewChild } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { WebsiteModel } from '../models/website.model';
import { WebsiteService } from '../services/website.service';
import { PackageLockModel } from '../models/package-lock.model';
import { MatSort, MatTableDataSource } from '@angular/material';

const PACKAGE_OPTIONS = {
  displayedColumns: ['vendor', 'repository', 'version', 'rootVersion', 'isPrivate', 'packagist']
};

@Component({
  selector: 'app-website-detail',
  templateUrl: './website-detail.component.html',
  styleUrls: ['./website-detail.component.scss']
})
export class WebsiteDetailComponent implements OnInit {

  website: WebsiteModel;

  packageOptions = PACKAGE_OPTIONS;
  packages: PackageLockModel[];

  dataSource: MatTableDataSource<PackageLockModel>;
  @ViewChild(MatSort) packageSort: MatSort;

  constructor(private route: ActivatedRoute, private ws: WebsiteService) {
  }

  ngOnInit() {
    this.route.params.subscribe(params => {
      this.ws.getOne(params.hash).subscribe(res => {
        this.website = res;
        this.packages = this.buildPackages(res.composerLock);

        this.dataSource = new MatTableDataSource(this.packages);
      });
    });
  }

  // add vendor and repo
  private buildPackages(composerLock: PackageLockModel[]) {
    const packages = [];
    for (const pkg of composerLock) {
      const splitted = pkg.name.split('/');
      packages.push({ ...pkg, vendor: splitted[0], repository: splitted[1] });
    }

    return packages;
  }

  sortPackages(event) {
    this.dataSource.data = this.packages.sort((a, b) => {
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

  applyFilter(value) {
    this.dataSource.filter = value.trim().toLowerCase();
    this.sortPackages({active: '', direction: 'asc'});
  }
}
