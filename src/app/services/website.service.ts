import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../environments/environment';
import { WebsiteModel } from '../models/website.model';

@Injectable({
  providedIn: 'root'
})
export class WebsiteService {

  constructor(private http: HttpClient) {
  }

  getAll() {
    return this.http.get<WebsiteModel[]>(environment.everestApi + '/website/all');
  }

  getOne(hash: string) {
    return this.http.get<WebsiteModel>(environment.everestApi + '/website/' + hash);
  }

  add(data: { url: string, token: string }) {
    return this.http.post(environment.everestApi + '/website/add', data);
  }
}
