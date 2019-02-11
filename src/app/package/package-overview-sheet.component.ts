import { Component } from '@angular/core';
import { MatBottomSheetRef } from '@angular/material';
import { ComposerService } from '../services/composer.service';

@Component({
  selector: 'app-package-overview-sheet',
  templateUrl: 'package-overview-sheet.html'
})
export class PackageOverviewSheetComponent {
  constructor(private sheetRef: MatBottomSheetRef<PackageOverviewSheetComponent>, private cs: ComposerService) {
  }
}
