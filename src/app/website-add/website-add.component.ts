import { Component, Inject, OnInit } from '@angular/core';
import { CONTAO_MANAGER, ContaoManagerService } from '../services/contao-manager.service';
import { ActivatedRoute, Router } from '@angular/router';
import { ConfigService } from '../services/config.service';
import { WebsiteModel } from '../models/website.model';
import { Form, FormControl, Validators } from '@angular/forms';
import { MAT_DIALOG_DATA, MatDialog } from '@angular/material';

@Component({
  selector: 'app-website-add',
  templateUrl: './website-add.component.html',
  styleUrls: ['./website-add.component.scss']
})
export class WebsiteAddComponent implements OnInit {

  url = new FormControl('', [Validators.required]);
  disableManagerButton = false;
  disableConfirmButton = false;

  constructor(
    protected dialog: MatDialog,
    protected cs: ConfigService,
    protected cms: ContaoManagerService,
    protected route: ActivatedRoute,
    protected router: Router
  ) {
  }

  ngOnInit(): void {
    if (this.route) {
      this.route.queryParams.subscribe(values => {
        if (values.token && values.origin) {
          this.cms.saveUrlAndToken(values.origin, values.token).subscribe((website: WebsiteModel) => {
            this.cs.getContaoConfig(website).subscribe((res: any) => {
              if (res.success) {
                this.router.navigateByUrl('/websites');
              }
            });
          });
        }
      });
    }
  }

  closeDialog() {
    this.dialog.closeAll();
  }

  setManagerUrl() {
    if (!this.disableManagerButton) {
      this.url.setValue(this.cms.getManagerUrl(this.url.value));
      this.disableManagerButton = !this.disableManagerButton;
    }
  }

  // check if the url contains the contao manager
  checkManagerAutofill() {
    this.disableManagerButton = this.url.value ? this.url.value.includes(CONTAO_MANAGER) : false;
  }

  openUrl() {
    window.open(this.url.value, '_blank');
  }
}
