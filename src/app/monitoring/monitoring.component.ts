import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA } from '@angular/material';
import { MonitoringModel } from '../models/monitoring.model';
import { MonitoringService } from '../services/monitoring.service';
import { WebsiteModel } from '../models/website.model';

@Component({
  selector: 'app-monitoring',
  templateUrl: './monitoring.component.html',
  styleUrls: ['./monitoring.component.scss']
})
export class MonitoringComponent implements OnInit {

  website: WebsiteModel;
  data: MonitoringModel[];

  series: object[];
  showXAxis = true;
  showYAxis = true;
  gradient = false;
  showLegend = true;
  showXAxisLabel = true;
  xAxisLabel = 'Datum';
  showYAxisLabel = true;
  yAxisLabel = 'Dauer';
  colorScheme = {
    domain: ['#ccc, #000', '#f00', '#ff0']
  };

  constructor(private ms: MonitoringService, @Inject(MAT_DIALOG_DATA) public injectedData: any) {
    this.website = injectedData.website;
  }

  ngOnInit() {
    this.ms.getAll(this.website).subscribe((res: MonitoringModel[]) => {
      this.data = res;
      this.buildSeries(res);
    });
  }

  buildSeries(res: MonitoringModel[]) {
    const entries = [];
    for (const entry of res) {
      entries.push({ name: entry.createdAt, value: entry.requestTimeInMs });
    }
    this.series = [{ name: this.website.cleanUrl, series: entries }];
  }
}
