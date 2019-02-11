import { Component, OnInit } from '@angular/core';
import { CONTAO_MANAGER, ContaoManagerService } from '../services/contao-manager.service';
import { ActivatedRoute, Router } from '@angular/router';
import { ConfigService } from '../services/config.service';
import { WebsiteModel } from '../models/website.model';
import { FormControl, Validators } from '@angular/forms';
import { MatDialog } from '@angular/material';

@Component({
  selector: 'app-website-add',
  templateUrl: './website-add.component.html',
  styleUrls: ['./website-add.component.scss']
})
export class WebsiteAddComponent implements OnInit {

  url = new FormControl('', [Validators.required]);
  disableManagerButton = false;
  disableOpenButton = true;

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
            console.log(website);
            if (website) {
              this.router.navigateByUrl('/websites');
            }
          });
        }
      });
    }
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
