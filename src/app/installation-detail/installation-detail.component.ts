import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { InstallationModel } from '../models/installation.model';
import { InstallationService } from '../services/installation.service';

@Component({
  selector: 'app-installation-detail',
  templateUrl: './installation-detail.component.html',
  styleUrls: ['./installation-detail.component.scss']
})
export class InstallationDetailComponent implements OnInit {

  installation: InstallationModel;

  constructor(private route: ActivatedRoute, private ws: InstallationService) {
  }

  ngOnInit() {
    this.route.params.subscribe(params => {
      this.ws.getOne(params.hash).subscribe(res => {
        this.installation = res;
      });
    });
  }
}
