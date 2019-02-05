import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { UserModel } from '../models/user.model';
import { environment } from '../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class UserService {

  constructor(private http: HttpClient) {
  }

  // details of logged in user
  getUser() {
    return this.http.get<UserModel>(environment.everestApi + '/user');
  }

  // list of all usernames
  getUsers() {
    return this.http.get<string[]>(environment.everestApi + '/user/all');
  }

  updateUserData(user: UserModel) {
    return this.http.post<UserModel>(environment.everestApi + '/user/update', user);
  }
}
