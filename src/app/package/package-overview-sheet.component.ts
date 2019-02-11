import { Component, EventEmitter, Inject, Output } from '@angular/core';
import { MAT_BOTTOM_SHEET_DATA, MatBottomSheetRef } from '@angular/material';
import { ComposerService } from '../services/composer.service';
import { WebsiteModel } from '../models/website.model';
import { interval } from 'rxjs';
import { TaskOutputModel } from '../models/task-output.model';
import { startWith } from 'rxjs/operators';

@Component({
  selector: 'app-package-overview-sheet',
  templateUrl: 'package-overview-sheet.html'
})
export class PackageOverviewSheetComponent {
  website: WebsiteModel;
  @Output() output: TaskOutputModel;

  constructor(private sheetRef: MatBottomSheetRef<PackageOverviewSheetComponent>,
              private cs: ComposerService,
              @Inject(MAT_BOTTOM_SHEET_DATA) public data: any) {
    this.website = data.website;
  }

  performUpdate(event: MouseEvent) {
    event.preventDefault();

    const task = {
      name: 'composer/update',
      config: {
        dry_run: false,
        require: [],
        remove: [],
        update: []
      },
      website: this.website.cleanUrl
    };

    console.log(task);

    this.cs.pushTask(task).subscribe(res => {
      if (res.status === 'active') {
        // close sheet
        this.sheetRef.dismiss(res);
      }
    });
  }
}
