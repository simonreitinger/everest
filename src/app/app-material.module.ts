import { NgModule } from '@angular/core';
import { MatButtonModule, MatCheckboxModule, MatDialogModule, MatFormFieldModule, MatInputModule } from '@angular/material';

const modules = [
  MatButtonModule,
  MatCheckboxModule,
  MatDialogModule,
  MatFormFieldModule,
  MatInputModule
];
@NgModule({
  imports: modules,
  exports: modules,
})
export class AppMaterialModule { }
