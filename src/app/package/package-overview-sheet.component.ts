import { Component, Inject, Output } from '@angular/core';
import { MAT_BOTTOM_SHEET_DATA, MatBottomSheetRef } from '@angular/material';
import { ComposerService } from '../services/composer.service';
import { WebsiteModel } from '../models/website.model';
import { TaskOutputModel } from '../models/task-output.model';

@Component({
  selector: 'app-package-overview-sheet',
  templateUrl: 'package-overview-sheet.html'
})
export class PackageOverviewSheetComponent {

  website: WebsiteModel;

  // selected packages
  packages: string[];

  dryRun = false;

  @Output() output: TaskOutputModel;

  constructor(private sheetRef: MatBottomSheetRef<PackageOverviewSheetComponent>,
              private composerService: ComposerService,
              @Inject(MAT_BOTTOM_SHEET_DATA) public data: any) {
    this.website = data.website;
    this.packages = data.packages;
    console.log(this.packages);
  }

  performUpdate(event: MouseEvent) {
    event.preventDefault();
    const task = this.composerService.buildTask('composer/update', this.website, [], [], [], this.dryRun);
    this.handleTask(task);
  }

  performUpdateSelected(event: MouseEvent) {
    event.preventDefault();
    const task = this.composerService.buildTask('composer/require', this.website, [], this.packages, [], this.dryRun);
    this.handleTask(task);
  }

  performInstall(event: MouseEvent) {
    event.preventDefault();
    const task = this.composerService.buildTask('composer/install', this.website, [], [], [], this.dryRun);
    this.handleTask(task);
  }

  performRemove(event: MouseEvent) {
    event.preventDefault();
    const task = this.composerService.buildTask('composer/update', this.website, [], [], this.packages, this.dryRun);
    this.handleTask(task);
  }

  handleTask(task: TaskModel) {
    this.composerService.pushTask(task).subscribe(res => {
      if (res.status === 'active') {
        // close sheet
        this.sheetRef.dismiss(res);
      }
    });
  }
}
