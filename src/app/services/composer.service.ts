import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../environments/environment';
import { TaskOutputModel } from '../models/task-output.model';
import { WebsiteModel } from '../models/website.model';

@Injectable({
  providedIn: 'root'
})
export class ComposerService {

  constructor(private http: HttpClient) {
  }

  pushTask(task: TaskModel) {
    return this.http.post<TaskOutputModel>(environment.everestApi + '/task', task);
  }

  getTaskStatus(website: WebsiteModel) {
    return this.http.get<TaskOutputModel>(environment.everestApi + '/task/' + website.hash);
  }

  buildTask(name: string, website: WebsiteModel, require: string[] = [], update: string[] = [], remove: string[] = [], dryRun: boolean = false) {
    return {
      name: name,
      config: {
        dry_run: dryRun,
        require: [],
        remove: [],
        update: []
      },
      website: website.cleanUrl
    };
  }
}
