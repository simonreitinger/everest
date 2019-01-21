import { Injectable } from '@angular/core';
import { ContaoManagerService, defaultTask } from './contao-manager.service';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { WebsiteModel } from '../models/website.model';

@Injectable({
  providedIn: 'root'
})
export class TaskService {

  constructor(private http: HttpClient, private cms: ContaoManagerService) { }

  composerUpdate(website: WebsiteModel) {
    const headers = new HttpHeaders({
      'Contao-Manager-Auth': website.token
    });
    console.log(this.http.put(this.cms.getManagerUrl(website.url) + '/api/task', this.getUpdateTask(), { headers, observe: 'response'}));
    return this.http.put(this.cms.getManagerUrl(website.url) + '/api/task', this.getUpdateTask(), { headers, observe: 'response'});
  }

  getUpdateTask() {
    return this.buildTask('composer/update');
  }

  private buildTask(name: string, config?) {
    return {
      name: name,
      config: config ? config : defaultTask.config
    };
  }
}
