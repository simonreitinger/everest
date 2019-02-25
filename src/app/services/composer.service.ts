import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../environments/environment';
import { TaskOutputModel } from '../models/task-output.model';
import { InstallationModel } from '../models/installation.model';
import { of, timer } from 'rxjs';
import { concatMap } from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class ComposerService {

  constructor(private http: HttpClient) {
  }

  pushTask(task: TaskModel) {
    return this.http.post<TaskOutputModel>(environment.everestApi + '/task', task);
  }

  getTaskStatus(installation: InstallationModel) {
    return this.http.get<TaskOutputModel>(environment.everestApi + '/task/' + installation.hash + '?XDEBUG_SESSION_START=PHPSTORM', { observe: 'response' });
  }

  buildTask(name: string, installation: InstallationModel, require: string[] = [], update: string[] = [], remove: string[] = [], dryRun: boolean = false): TaskModel {
    return {
      name,
      config: {
        dry_run: dryRun,
        require,
        remove,
        update
      },
      installation: installation.cleanUrl
    };
  }
}