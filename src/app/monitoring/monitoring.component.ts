import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA, PageEvent } from '@angular/material';
import { MonitoringModel } from '../models/monitoring.model';
import { MonitoringService } from '../services/monitoring.service';
import { WebsiteModel } from '../models/website.model';
import { Chart } from 'chart.js';

@Component({
  selector: 'app-monitoring',
  templateUrl: './monitoring.component.html',
  styleUrls: ['./monitoring.component.scss']
})
export class MonitoringComponent implements OnInit {

  website: WebsiteModel;
  data: MonitoringModel[];
  chart: any;

  // chart pagination settings
  perPage = 7;
  offset = 0;

  constructor(private ms: MonitoringService, @Inject(MAT_DIALOG_DATA) public injectedData: any) {
    this.website = injectedData.website;
  }

  ngOnInit() {
    this.ms.getAll(this.website).subscribe((res: MonitoringModel[]) => {
      this.data = res;
      console.log(res);
      this.buildChart();
    });
  }

  buildData() {
    const data = [];
    const labels = [];

    for (const entry of this.getCurrentViewData()) {
      const date = new Date(entry.createdAt);
      labels.push(date.getHours() + ':' + date.getMinutes());
      data.push(entry.requestTimeInMs);
    }

    return {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: this.website.cleanUrl,
          data: data,
          backgroundColor: 'transparent',
          borderColor: this.website.themeColor ? this.website.themeColor : '#5db7f4'
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
