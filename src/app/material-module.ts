import { NgModule } from '@angular/core';
import {
  MatBottomSheetModule,
  MatButtonModule,
  MatCheckboxModule,
  MatDialogModule, MatExpansionModule,
  MatFormFieldModule, MatIconModule,
  MatInputModule, MatListModule, MatMenuModule,
  MatPaginatorModule, MatProgressSpinnerModule, MatSortModule, MatTableModule
} from '@angular/material';

const modules = [
  MatBottomSheetModule,
  MatButtonModule,
  MatCheckboxModule,
  MatDialogModule,
  MatExpansionModule,
  MatFormFieldModule,
  MatIconModule,
  MatInputModule,
  MatListModule,
  MatMenuModule,
  MatPaginatorModule,
  MatProgressSpinnerModule,
  MatSortModule,
  MatTableModule
];

@NgModule({
  imports: modules,
  exports: modules
})
export class MaterialModule { }
