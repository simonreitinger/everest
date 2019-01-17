import { Component, OnInit } from '@angular/core';
import { WebsiteService } from '../../services/website.service';
import { NgForm } from '@angular/forms';

@Component({
  selector: 'app-website-add',
  templateUrl: './website-add.component.html',
  styleUrls: ['./website-add.component.scss']
})
export class WebsiteAddComponent {

  constructor(private ws: WebsiteService) { }

  add(form: NgForm) {
    this.ws.add(form.value).subscribe(res => {
      console.log(res);
    });
  }
}
