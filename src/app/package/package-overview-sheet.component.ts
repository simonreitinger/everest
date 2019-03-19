import { Component, Inject, Output } from '@angular/core';
import { MAT_BOTTOM_SHEET_DATA, MatBottomSheetRef } from '@angular/material';
import { ComposerService } from '../services/composer.service';
import { InstallationModel } from '../models/installation.model';
import { TaskOutputModel } from '../models/task-output.model';
import { TaskModel } from '../models/task.model';

@Component({
  selector: 'app-package-overview-sheet',
  templateUrl: 'package-overview-sheet.html'
})
export class PackageOverviewSheetComponent {

  installation: InstallationModel;

  // selected packages
  packages: string[];

  dryRun = false;

  @Output() output: TaskOutputModel;

  constructor(private sheetRef: MatBottomSheetRef<PackageOverviewSheetComponent>,
              private composerService: ComposerService,
              @Inject(MAT_BOTTOM_SHEET_DATA) public data: any) {
    this.installation = data.installation;
    this.packages = data.packages;
  }

  performUpdate(event: MouseEvent) {
    event.preventDefault();
    const task = this.composerService.buildTask('composer/update', this.installation, [], [], [], this.dryRun);
    this.handleTask(task);
  }

  performUpdateSelected(event: MouseEvent) {
    event.preventDefault();
    const task = this.composerService.buildTask('composer/update', this.installation, [], this.packages, [], this.dryRun);
    this.handleTask(task);
  }

  performInstall(event: MouseEvent) {
    event.preventDefault();
    const task = this.composerService.buildTask('composer/install', this.installation, [], [], [], this.dryRun);
    this.handleTask(task);
  }

  performRemove(event: MouseEvent) {
    event.preventDefault();
    const task = this.composerService.buildTask('composer/update', this.installation, [], [], this.packages, this.dryRun);
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
