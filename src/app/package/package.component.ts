import { Component, Input, OnInit, ViewChild } from '@angular/core';
import { PackageLockModel } from '../models/package-lock.model';
import { MatBottomSheet, MatDialog, MatSort, MatTableDataSource } from '@angular/material';
import { PackageOverviewSheetComponent } from './package-overview-sheet.component';
import { InstallationModel } from '../models/installation.model';
import { TaskOutputModel } from '../models/task-output.model';
import { ConsoleOutputComponent } from '../console-output/console-output.component';

const PACKAGE_OPTIONS = {
  displayedColumns: ['checked', 'vendor', 'repository', 'version', 'rootVersion', 'isPrivate', 'packagist']
};

@Component({
  selector: 'app-package',
  templateUrl: './package.component.html',
  styleUrls: ['./package.component.scss']
})
export class PackageComponent implements OnInit {

  @Input() installation: InstallationModel;
  packages: PackageLockModel[];

  output: TaskOutputModel;

  packageOptions = PACKAGE_OPTIONS;
  dataSource: MatTableDataSource<PackageLockModel>;
  @ViewChild(MatSort) packageSort: MatSort;

  showAll: boolean;

  constructor(private bottomSheet: MatBottomSheet, private dialog: MatDialog) {
  }

  ngOnInit() {
    this.packages = this.buildPackages(this.installation.composerLock);
    this.dataSource = new MatTableDataSource(this.filterRootPackages());
  }

  sortPackages(event) {
    this.dataSource.data = this.dataSource.data.sort((a, b) => {
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

    // empty values make the sorting go crazy
    if (value) {
      this.sortPackages({ active: 'vendor', direction: 'asc' });
    }
  }

  showDependencies() {
    if (this.showAll) {
      this.dataSource.data = this.packages;
    } else {
      this.dataSource.data = this.filterRootPackages();
    }
  }

  filterRootPackages() {
    return this.packages.filter(pkg => pkg.rootVersion);
  }

  openSheet() {
    const bottomSheetRef = this.bottomSheet.open(PackageOverviewSheetComponent, {
      data:
        {
          installation: this.installation,
          packages: this.packages.filter(pkg => pkg.checked).map(p => p.name)
        }
    });

    bottomSheetRef.afterDismissed().subscribe(output => {
      if (output) {
        this.output = output;
        this.openConsoleDialog();
      }
    });
  }

  openConsoleDialog() {
    const dialogRef = this.dialog.open(ConsoleOutputComponent, {
      height: '500px',
      width: '800px',
      data: {
        output: this.output,
        installation: this.installation
      },
      disableClose: !this.output.autoclose
    });
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
}
