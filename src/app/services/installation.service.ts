import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../environments/environment';
import { InstallationModel } from '../models/installation.model';

@Injectable({
  providedIn: 'root'
})
export class InstallationService {

  constructor(private http: HttpClient) {
  }

  getAll() {
    return this.http.get<InstallationModel[]>(environment.apiUrl + '/installation/all');
  }

  getAllByLimitAndOffset(limit = 10, offset = 0) {
    return this.http.get<InstallationModel[]>(environment.apiUrl + '/installation/all', {params: {limit: limit.toString(), offset: offset.toString()}});
  }

  getOne(hash: string) {
    return this.http.get<InstallationModel>(environment.apiUrl + '/installation/' + hash);
  }

  add(data: { url: string, token: string }) {
    return this.http.post(environment.apiUrl + '/installation/add', data);
  }

  remove(hash: string) {
    return this.http.delete(environment.apiUrl + '/installation/delete/' + hash);
  }

  getCount() {
    return this.http.get(environment.apiUrl + '/installation/count');
  }
}
