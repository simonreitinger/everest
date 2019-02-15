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
    return this.http.get<InstallationModel[]>(environment.everestApi + '/installation/all');
  }

  getOne(hash: string) {
    return this.http.get<InstallationModel>(environment.everestApi + '/installation/' + hash);
  }

  add(data: { url: string, token: string }) {
    return this.http.post(environment.everestApi + '/installation/add', data);
  }
}
