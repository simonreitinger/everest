import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class WebsiteService {

  private list: [];

  constructor(private http: HttpClient) {
  }

  get() {
    return this.http.get(environment.everestApi + 'websites');
  }

  add(data) {
    return this.http.post(environment.everestApi + 'websites/add', data);
  }
}
