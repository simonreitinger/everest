import { Component, ElementRef, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA } from '@angular/material';
import { MonitoringModel } from '../models/monitoring.model';
import { MonitoringService } from '../services/monitoring.service';
import { WebsiteModel } from '../models/website.model';
import { Chart } from 'chart.js';
import { defaultTask } from '../services/contao-manager.service';

@Component({
  selector: 'app-monitoring',
  templateUrl: './monitoring.component.html',
  styleUrls: ['./monitoring.component.scss']
})
export class MonitoringComponent implements OnInit {

  website: WebsiteModel;
  data: MonitoringModel[];
  chart: any;

  constructor(private ms: MonitoringService, @Inject(MAT_DIALOG_DATA) public injectedData: any) {
    this.website = injectedData.website;
  }

  ngOnInit() {
    this.ms.getAll(this.website).subscribe((res: MonitoringModel[]) => {
      this.data = res;
      this.buildChart();
    });
  }

  buildData() {
    const data = [];
    const labels = [];
    for (const entry of this.data) {
      labels.push(entry.createdAt);
      data.push(entry.requestTimeInMs);
    }

    return {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: this.website.cleanUrl,
          data: data,
          backgroundColor: [this.website.themeColor ? this.website.themeColor : '#5db7f4']
        }]
      },
      options: {

      }
    };
  }

  buildChart() {
    const canvas = document.getElementById('monitoringChart');
    this.chart = new Chart(canvas, this.buildData());
  }
}
