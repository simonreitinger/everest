import { Component } from '@angular/core';
import { CONTAO_MANAGER, ContaoManagerService } from '../services/contao-manager.service';
import { Router } from '@angular/router';
import { ConfigService } from '../services/config.service';
import { FormControl, Validators } from '@angular/forms';
import { MatDialog } from '@angular/material';

@Component({
  selector: 'app-installation-add',
  templateUrl: './installation-add.component.html',
  styleUrls: ['./installation-add.component.scss']
})
export class InstallationAddComponent {

  url = new FormControl('', [Validators.required]);
  disableManagerButton = false;
  disableOpenButton = true;

  constructor(
    protected dialog: MatDialog,
    protected cs: ConfigService,
    protected cms: ContaoManagerService,
    protected router: Router
  ) {
  }

  closeDialog() {
    this.dialog.closeAll();
  }

  setManagerUrl() {
    this.url.setValue(this.cms.getManagerUrl(this.url.value));
    this.disableManagerButton = true;
  }

  // activate / deactivate buttons
  checkUrl() {
    this.disableManagerButton = this.url.value ? this.url.value.includes(CONTAO_MANAGER) : false;
    this.isUrlValid();
  }

  isUrlValid() {
    this.disableOpenButton = !this.cms.isValidUrl(this.url.value);
  }

  openUrl() {
    window.open(this.cms.getRegisterUrl(this.url.value), '_blank');
  }
}
