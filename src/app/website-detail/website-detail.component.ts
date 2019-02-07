import { Component, OnInit, ViewChild } from '@angular/core';
import { ActivatedRoute, Data } from '@angular/router';
import { WebsiteModel } from '../models/website.model';
import { WebsiteService } from '../services/website.service';
import { PackageLockModel } from '../models/package-lock.model';
import { MatSort, MatTableDataSource } from '@angular/material';
import { DataSource } from '@angular/cdk/table';
import { CollectionViewer } from '@angular/cdk/collections';
import { merge, Observable, of } from 'rxjs';

const PACKAGE_OPTIONS = {
  displayedColumns: ['vendor', 'repository', 'version', 'rootVersion', 'isPrivate']
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

  @ViewChild(MatSort) sort: MatSort;

  constructor(private route: ActivatedRoute, private ws: WebsiteService) {
  }

  ngOnInit() {
    this.route.params.subscribe(params => {
      this.ws.getOne(params.hash).subscribe(res => {
        this.website = res;
        this.packages = this.buildPackages(res.composerLock);
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
}
