import { Component, Inject, OnInit, ViewChild } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material';
import { TaskOutputModel } from '../models/task-output.model';
import { ComposerService } from '../services/composer.service';
import { InstallationModel } from '../models/installation.model';
import { interval, of, Subscription } from 'rxjs';
import { startWith, switchMap } from 'rxjs/operators';

@Component({
  selector: 'app-console-output',
  templateUrl: './console-output.component.html',
  styleUrls: ['./console-output.component.scss']
})
export class ConsoleOutputComponent implements OnInit {

  installation: InstallationModel;
  output: TaskOutputModel;

  interval: Subscription;

  @ViewChild('console') console;

  constructor(private dialogRef: MatDialogRef<ConsoleOutputComponent>,
              private composerService: ComposerService,
              @Inject(MAT_DIALOG_DATA) public data: any) {
    this.installation = data.installation;
    this.output = data.output;
  }

  ngOnInit() {
    this.interval = interval(5000).pipe(startWith(0)).subscribe(() => {
      this.composerService.getTaskStatus(this.installation).subscribe(res => {
        console.log(res.body);
        this.output = res.body;
        if (this.output.status !== 'active') {
          this.interval.unsubscribe();
          this.dialogRef.disableClose = false;
        }
        this.updateScrollHeight();
      });
    });
  }

  updateScrollHeight() {
    this.console.nativeElement.scrollTop = this.console.nativeElement.clientHeight;
  }
}
