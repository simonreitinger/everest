import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { InstallationModel } from '../models/installation.model';
import { InstallationService } from '../services/installation.service';
import { ConfigService } from '../services/config.service';

@Component({
  selector: 'app-installation-detail',
  templateUrl: './installation-detail.component.html',
  styleUrls: ['./installation-detail.component.scss']
})
export class InstallationDetailComponent implements OnInit {

  installation: InstallationModel;
  updating = false;

  constructor(private route: ActivatedRoute, private ws: InstallationService, private cs: ConfigService) {
  }

  ngOnInit() {
    this.route.params.subscribe(params => {
      this.ws.getOne(params.hash).subscribe(res => {
        this.installation = res;
      });
    });
  }

  update() {
    this.updating = true;
    this.cs.forceUpdate(this.installation).subscribe((res: InstallationModel) => {
      this.updating = false;
      this.installation = res;
    });
  }
}
