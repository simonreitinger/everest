import { Component, Input, OnInit } from '@angular/core';
import { InstallationModel } from '../models/installation.model';

@Component({
  selector: 'app-detail-info',
  templateUrl: './detail-info.component.html',
  styleUrls: ['./detail-info.component.scss']
})
export class DetailInfoComponent implements OnInit {

  @Input() installation: InstallationModel;

  constructor() {
  }

  ngOnInit() {
  }

}
