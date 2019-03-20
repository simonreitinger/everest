import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { ContaoManagerService } from '../services/contao-manager.service';

@Component({
  selector: 'app-installation-register',
  templateUrl: './installation-register.component.html',
  styleUrls: ['./installation-register.component.scss']
})
export class InstallationRegisterComponent implements OnInit {

  url = '';

  constructor(
    protected route: ActivatedRoute,
    protected router: Router,
    protected cms: ContaoManagerService
  ) {
  }

  ngOnInit() {
    if (this.route) {
      this.route.queryParams.subscribe(values => {
        if (values.token && values.origin) {
          this.url = values.origin;
          this.cms.saveUrlAndToken(values.origin, values.token).subscribe((res: any) => {
            if (res.success) {
              this.router.navigateByUrl('/installations');
            }
          });
        }
      });
    }
  }


}
