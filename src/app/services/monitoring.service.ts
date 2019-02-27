import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { InstallationModel } from '../models/installation.model';
import { environment } from '../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class MonitoringService {

  constructor(private http: HttpClient) { }

  // fetch all relevant monitoring stats for installation
  getAll(installation: InstallationModel) {
    return this.http.get(environment.apiUrl + '/monitoring/' + installation.hash);
  }

  // fetch the last monitoring stats for a installation
  getLast(installation: InstallationModel) {
    return this.http.get(environment.apiUrl + '/monitoring/' + installation.hash + '/current');
  }
}
