import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../environments/environment';
import { map } from 'rxjs/operators';
import { Router } from '@angular/router';
import * as jwt_decode from 'jwt-decode';

const LOCAL_STORAGE_TOKEN_KEY = 'token';

@Injectable({
  providedIn: 'root'
})
export class AuthenticationService {

  interval: any;

  constructor(private http: HttpClient, private router: Router) {
  }

  login(username: string, password: string) {
    const credentials = { username, password };
    return this.http.post<any>(environment.everestApi + '/auth/login', credentials, { observe: 'response' }).pipe(
      map(res => {
        if (res.body.token) {
          this.setToken(res.body.token);
          return true;
        }
        return false;
      })
    );
  }

  // retrieve a new token
  refreshToken() {
    return this.http.get<any>(environment.everestApi + '/auth/token', { observe: 'response' }).pipe(
      map(res => {
        if (res.body.token) {
          this.setToken(res.body.token);
          console.log('new token: ' + res.body.token);
        }
      })
    );
  }

  getTimeUntilLogout() {
    const exp = (this.getDecodedToken()).exp;
    const now = Math.floor(Date.now() / 1000);

    return exp - now;
  }

  isTokenValid() {
    const exp = this.getExpirationDate();

    return exp.valueOf() > (new Date()).valueOf();
  }

  // checks for valid login every interval
  startLoginInterval() {
    this.interval = setInterval(() => {
      this.isLoggedIn();
    }, 5000);
  }

  endLoginInterval() {
    clearInterval(this.interval);
  }

  getExpirationDate() {
    const exp = new Date(0);
    const token = this.getDecodedToken();
    if (token) {
      exp.setUTCSeconds(token.exp);
    }

    return exp;
  }

  getToken() {
    return localStorage.getItem(LOCAL_STORAGE_TOKEN_KEY) ? localStorage.getItem(LOCAL_STORAGE_TOKEN_KEY) : null;
  }

  getDecodedToken() {
    return this.getToken() ? jwt_decode(this.getToken()) : null;
  }

  setToken(token) {
    localStorage.setItem(LOCAL_STORAGE_TOKEN_KEY, token);
  }

  logout() {
    localStorage.removeItem(LOCAL_STORAGE_TOKEN_KEY);
  }

  // checks the exp property of the token for a valid date
  private isLoggedIn(): boolean {
    const valid = this.isTokenValid();

    if (!valid) {
      this.logout();
      this.router.navigateByUrl('/login');
    }

    return valid;
  }
}
