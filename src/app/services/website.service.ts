import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class WebsiteService {

  constructor(private http: HttpClient) {
  }

  getAll() {
    return this.http.get(environment.everestApi + '/website/all');
  }

  add(data: { url: string, token: string }) {
    return this.http.post(environment.everestApi + '/website/add', data);
  }
}
