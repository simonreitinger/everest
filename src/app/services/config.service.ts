import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { InstallationModel } from '../models/installation.model';
import { ContaoManagerService } from './contao-manager.service';
import { environment } from '../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class ConfigService {

  constructor(private http: HttpClient) {
  }

  forceUpdate(installation: InstallationModel) {
    return this.http.get(environment.apiUrl + '/config/' + installation.hash);
  }


}
