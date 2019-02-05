import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { WebsiteModel } from '../models/website.model';
import { ContaoManagerService } from './contao-manager.service';
import { environment } from '../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class ConfigService {

  constructor(private http: HttpClient) {
  }

  getContaoConfig(website: WebsiteModel) {
    return this.http.get(environment.everestApi + '/config/' + website.hash);
  }


}
