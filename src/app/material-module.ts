import { NgModule } from '@angular/core';
import {
  MatButtonModule,
  MatCheckboxModule,
  MatDialogModule,
  MatFormFieldModule,
  MatInputModule, MatPaginatorIntl,
  MatPaginatorModule
} from '@angular/material';
import { registerLocaleData } from '@angular/common';
import localeDe from '@angular/common/locales/de';

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
  exports: modules
})
export class MaterialModule { }
