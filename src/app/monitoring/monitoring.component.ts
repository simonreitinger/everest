import { Component, Input, OnInit } from '@angular/core';
import { PageEvent } from '@angular/material';
import { MonitoringModel } from '../models/monitoring.model';
import { MonitoringService } from '../services/monitoring.service';
import { InstallationModel } from '../models/installation.model';
import { Chart } from 'chart.js';

@Component({
  selector: 'app-monitoring',
  templateUrl: './monitoring.component.html',
  styleUrls: ['./monitoring.component.scss']
})
export class MonitoringComponent implements OnInit {

  @Input() installation: InstallationModel;
  data: MonitoringModel[];
  chart: any;

  // chart pagination settings
  perPage = 7;
  offset = 0;

  constructor(private ms: MonitoringService) {
  }

  ngOnInit() {
    this.ms.getAll(this.installation).subscribe((res: MonitoringModel[]) => {
      this.data = res;
      this.buildChart();
    });
  }

  buildData() {
    const data = [];
    const labels = [];

    for (const entry of this.getCurrentViewData()) {
      const date = new Date(entry.createdAt);
      labels.push(date.getHours() + ':' + (date.getMinutes() < 10 ? '0' : '') + date.getMinutes());
      data.push(entry.requestTimeInMs);
    }

    return {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: this.installation.cleanUrl,
          data: data,
          backgroundColor: 'transparent',
          borderColor: this.installation.themeColor ? this.installation.themeColor : '#5db7f4'
        }]
      },
      options: {
        legend: {
          display: false
        },
        scales: {
          yAxes: [{
            ticks: {
              beginAtZero: true,
              callback: (value, index, values) => {
                return value + ' ms';
              }
            }
          }]
        },
        tooltips: {
          enabled: false
        }
      }
    };
  }

  buildChart() {
    const canvas = document.getElementById('monitoringChart');
    this.chart = new Chart(canvas, this.buildData());
  }

  getCurrentViewData() {
    return this.data.slice(this.offset, this.offset + this.perPage).reverse();
  }

  setOffset(event: PageEvent) {
    this.offset = event.pageIndex * this.perPage;
    this.buildChart();
  }
}
