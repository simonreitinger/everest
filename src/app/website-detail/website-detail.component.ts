import { Component, OnInit, ViewChild } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { WebsiteModel } from '../models/website.model';
import { WebsiteService } from '../services/website.service';
import { PackageLockModel } from '../models/package-lock.model';
import { MatSort, MatTableDataSource } from '@angular/material';

@Component({
  selector: 'app-website-detail',
  templateUrl: './website-detail.component.html',
  styleUrls: ['./website-detail.component.scss']
})
export class WebsiteDetailComponent implements OnInit {

  website: WebsiteModel;

  constructor(private route: ActivatedRoute, private ws: WebsiteService) {
  }

  ngOnInit() {
    this.route.params.subscribe(params => {
      this.ws.getOne(params.hash).subscribe(res => {
        this.website = res;
      });
    });
  }
}
