import { Component, Input, OnInit } from '@angular/core';
import { WebsiteModel } from '../models/website.model';
import { ConfigService } from '../services/config.service';
import { MonitoringService } from '../services/monitoring.service';
import { MonitoringModel } from '../models/monitoring.model';
import { MatDialog } from '@angular/material';
import { MonitoringComponent } from '../monitoring/monitoring.component';

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
      }
    });
  }
}
