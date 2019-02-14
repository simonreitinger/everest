import { Component, Inject, OnInit } from '@angular/core';
import { MonitoringService } from '../services/monitoring.service';
import { MAT_DIALOG_DATA } from '@angular/material';
import { WebsiteModel } from '../models/website.model';

@Component({
  selector: 'app-monitoring-dialog',
  templateUrl: './monitoring-dialog.component.html',
  styleUrls: ['./monitoring-dialog.component.scss']
})
export class MonitoringDialogComponent implements OnInit {

  website: WebsiteModel;

  constructor(@Inject(MAT_DIALOG_DATA) public data: any) {
    this.website = data.website;
  }

  ngOnInit() {
  }

}
