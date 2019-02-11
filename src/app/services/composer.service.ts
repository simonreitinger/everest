import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class ComposerService {

  private tasks: TaskModel[];

  constructor(private http: HttpClient) {
    this.fetchTasks();
  }

  fetchTasks() {
    this.http.get<TaskModel[]>(environment.everestApi + '/task/all').subscribe(res => {
      this.tasks = res;
    });
  }

  getTasks() {
    return this.tasks;
  }

  pushTask(task: TaskModel) {
    this.tasks.push(task);

    return this.http.post(environment.everestApi + '/task', task);
  }
}
