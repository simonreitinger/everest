import { Component, OnInit } from '@angular/core';
import { WebsiteService } from '../../services/website.service';
import { ContaoManagerService } from '../../services/contao-manager.service';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
  selector: 'app-website-add',
  templateUrl: './website-add.component.html',
  styleUrls: ['./website-add.component.scss']
})
export class WebsiteAddComponent implements OnInit {

  url: string;

  constructor(private cms: ContaoManagerService, private route: ActivatedRoute, private router: Router) { }

  ngOnInit(): void {
    this.route.queryParams.subscribe(values => {
      if (values.token && values.origin) {
        this.cms.saveUrlAndToken(values.origin, values.token).subscribe((res: any) => {
          console.log(res);
          if (res.success) {
            this.router.navigateByUrl('/websites');
          }
        });
      }
    });
  }

  openManager() {
    window.open(this.cms.getRegisterUrl(this.url));
  }
}
