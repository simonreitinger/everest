import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { environment } from '../../environments/environment';
import { map } from 'rxjs/operators';
import { UserModel } from '../models/user.model';
import { Router } from '@angular/router';
import { observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class AuthenticationService {

  constructor(private http: HttpClient, private router: Router) {
  }

  login(username: string, password: string) {
    const credentials = { username, password };
    return this.http.post<any>(environment.everestApi + '/auth/login', credentials, { observe: 'response' }).pipe(
      map(res => {
        console.log(res.status);
        if (res.body.token) {
          localStorage.setItem('token', res.body.token);
          return true;
        }
        return false;
      })
    );
  }

  isLoggedIn(): boolean {
    return localStorage.getItem('token') ? true : false;
  }

  logout() {
    localStorage.removeItem('user');
  }
}
