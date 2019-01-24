import { Component, Input, OnInit } from '@angular/core';
import { WebsiteModel } from '../../models/website.model';
import { ConfigService } from '../../services/config.service';
import { MonitoringService } from '../../services/monitoring.service';
import { MonitoringModel } from '../../models/monitoring.model';

@Component({
  selector: 'app-website',
  templateUrl: './website.component.html',
  styleUrls: ['./website.component.scss']
})
export class WebsiteComponent implements OnInit {

  @Input() website: WebsiteModel;
  @Input() phpVersions: string[];
  monitoring: MonitoringModel;

  constructor(private ms: MonitoringService) { }

  ngOnInit() {
    this.ms.getLast(this.website).subscribe((res: MonitoringModel) => {
        this.monitoring = res;
    });
    console.log(this.website);
  }

}
