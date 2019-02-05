import { Component, Input, OnInit } from '@angular/core';
import { WebsiteModel } from '../models/website.model';
import { MonitoringService } from '../services/monitoring.service';
import { MonitoringModel } from '../models/monitoring.model';
import { MonitoringComponent } from '../monitoring/monitoring.component';
import { MatDialog } from '@angular/material';

@Component({
  selector: 'app-website',
  templateUrl: './website.component.html',
  styleUrls: ['./website.component.scss']
})
export class WebsiteComponent implements OnInit {

  @Input() website: WebsiteModel;
  @Input() phpVersions: string[];
  monitoring: MonitoringModel;

  constructor(private ms: MonitoringService, private dialog: MatDialog) { }

  ngOnInit() {
    this.ms.getLast(this.website).subscribe((res: MonitoringModel) => {
        this.monitoring = res;
    });
  }

  openMonitoringDialog() {
    this.dialog.open(MonitoringComponent, {
      data: {
        website: this.website
      },
      height: '450px',
      width: '800px'
    });
  }
}
