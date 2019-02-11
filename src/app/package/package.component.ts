import { Component, Input, OnInit, ViewChild } from '@angular/core';
import { PackageLockModel } from '../models/package-lock.model';
import { MatBottomSheet, MatSort, MatTableDataSource } from '@angular/material';
import { PackageOverviewSheetComponent } from './package-overview-sheet.component';

const PACKAGE_OPTIONS = {
  displayedColumns: ['checked', 'vendor', 'repository', 'version', 'rootVersion', 'isPrivate', 'packagist']
};

@Component({
  selector: 'app-package',
  templateUrl: './package.component.html',
  styleUrls: ['./package.component.scss']
})
export class PackageComponent implements OnInit {

  @Input() composerLock: PackageLockModel[];
  packages: PackageLockModel[];
  checkedPackages: PackageLockModel[];

  packageOptions = PACKAGE_OPTIONS;
  dataSource: MatTableDataSource<PackageLockModel>;
  @ViewChild(MatSort) packageSort: MatSort;

  constructor(private bottomSheet: MatBottomSheet) {
  }

  ngOnInit() {
    this.packages = this.buildPackages(this.composerLock);
    this.dataSource = new MatTableDataSource(this.packages);

    console.log(this.packages);
  }

  // add vendor and repo
  private buildPackages(composerLock: PackageLockModel[]) {
    const packages = [];
    for (const pkg of composerLock) {
      const splitted = pkg.name.split('/');
      packages.push({ ...pkg, vendor: splitted[0], repository: splitted[1], checked: false });
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

  openSheet() {
    this.bottomSheet.open(PackageOverviewSheetComponent);
  }

  updateCheckedPackages() {
    this.checkedPackages = this.packages.filter(pkg => pkg.checked);
  }
}

