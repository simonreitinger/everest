import { NgModule } from '@angular/core';
import {
  MatButtonModule,
  MatCheckboxModule,
  MatDialogModule,
  MatFormFieldModule,
  MatInputModule, MatPaginatorModule,
} from '@angular/material';

const modules = [
  MatButtonModule,
  MatCheckboxModule,
  MatDialogModule,
  MatFormFieldModule,
  MatInputModule,
  MatPaginatorModule
];
@NgModule({
  imports: modules,
  exports: modules,
})
export class MaterialModule { }
