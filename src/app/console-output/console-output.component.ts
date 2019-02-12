import { Component, ElementRef, Inject, OnInit, ViewChild } from '@angular/core';
import { MAT_DIALOG_DATA } from '@angular/material';
import { TaskOutputModel } from '../models/task-output.model';
import { ComposerService } from '../services/composer.service';
import { WebsiteModel } from '../models/website.model';
import { interval, Observable, Subscription } from 'rxjs';
import { startWith } from 'rxjs/operators';

@Component({
  selector: 'app-console-output',
  templateUrl: './console-output.component.html',
  styleUrls: ['./console-output.component.scss']
})
export class ConsoleOutputComponent implements OnInit {

  website: WebsiteModel;
  output: TaskOutputModel;

  interval: Subscription;

  @ViewChild('console') console;

  constructor(private cs: ComposerService, @Inject(MAT_DIALOG_DATA) public data: any) {
    this.website = data.website;
    this.output = data.output;
  }

  ngOnInit() {
    this.interval = interval(5000).pipe(startWith(0)).subscribe(() => {
      this.cs.getTaskStatus(this.website).subscribe(output => {
        this.output = output;
        if (output.status !== 'active') {
          this.interval.unsubscribe();
        }
        this.updateScrollHeight();
      });
    });
  }

  updateScrollHeight() {
    this.console.nativeElement.scrollTop = this.console.nativeElement.clientHeight;
  }
}
