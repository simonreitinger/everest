import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { WebsiteModel } from '../models/website.model';
import { environment } from '../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class MonitoringService {

  constructor(private http: HttpClient) { }

  // fetch all relevant monitoring stats for a website
  getAll(website: WebsiteModel) {
    return this.http.get(environment.everestApi + '/monitoring/' + website.hash);
  }

  // fetch the last monitoring stats for a website
  getLast(website: WebsiteModel) {
    return this.http.get(environment.everestApi + '/monitoring/' + website.hash + '/current');
  }
}
