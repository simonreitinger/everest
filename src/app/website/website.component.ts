import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { WebsiteService } from '../services/website.service';
import { TaskService } from '../services/task.service';
import { WebsiteModel } from '../models/website.model';
import { ContaoManagerService } from '../services/contao-manager.service';

@Component({
  selector: 'app-website',
  templateUrl: './website.component.html',
  styleUrls: ['./website.component.scss']
})
export class WebsiteComponent implements OnInit {

  private websites: [];

  constructor(private ws: WebsiteService, private ts: TaskService, private cms: ContaoManagerService) {
  }

  ngOnInit() {
    this.ws.getAll().subscribe((res: any) => {
      this.websites = res;
    });
  }

  updateTask(website: WebsiteModel) {
    this.ts.composerUpdate(website).subscribe(res => {
      console.dir(res);
    });
  }
}
