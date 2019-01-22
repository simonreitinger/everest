import { Component, Input, OnInit } from '@angular/core';
import { WebsiteModel } from '../../models/website.model';
import { ConfigService } from '../../services/config.service';

@Component({
  selector: 'app-website',
  templateUrl: './website.component.html',
  styleUrls: ['./website.component.scss']
})
export class WebsiteComponent implements OnInit {

  @Input() website: WebsiteModel;

  constructor(private cs: ConfigService) { }

  ngOnInit() {
    this.cs.getContaoConfig(this.website).subscribe(res => {
      console.log('test', res);
    });
  }

}
