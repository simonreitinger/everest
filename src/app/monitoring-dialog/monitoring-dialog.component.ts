import { Component, Inject, OnInit } from '@angular/core';
import { MonitoringService } from '../services/monitoring.service';
import { MAT_DIALOG_DATA } from '@angular/material';
import { InstallationModel } from '../models/installation.model';

@Component({
  selector: 'app-monitoring-dialog',
  templateUrl: './monitoring-dialog.component.html',
  styleUrls: ['./monitoring-dialog.component.scss']
})
export class MonitoringDialogComponent implements OnInit {

  installation: InstallationModel;

  constructor(@Inject(MAT_DIALOG_DATA) public data: any) {
    this.installation = data.installation;
  }

  ngOnInit() {
  }

}
