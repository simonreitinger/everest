import { HttpErrorResponse, HttpEvent, HttpHandler, HttpInterceptor, HttpRequest } from '@angular/common/http';
import { Observable, throwError } from 'rxjs';
import { catchError } from 'rxjs/operators';
import { AuthenticationService } from '../services/authentication.service';
import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class RequestInterceptor implements HttpInterceptor {

  constructor(private auth: AuthenticationService) {
  }

  // sets token, content type and accept headers.
  intercept(req: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    if (this.auth.getToken()) {
      if (this.auth.getTimeUntilLogout() < 300) {
        this.auth.refreshToken();
      }
      req = req.clone({
        headers: req.headers.set(
          'Authorization',
          'Bearer ' + this.auth.getToken())
      });
    }

    if (!req.headers.has('Content-Type')) {
      req = req.clone({ headers: req.headers.set('Content-Type', 'application/json') });
    }
    req = req.clone({ headers: req.headers.set('Accept', 'application/json') });

    if (environment.production === false) {
      req = req.clone({ setParams: { 'XDEBUG_SESSION_START': 'PHPSTORM' } });
    }

    return next.handle(req).pipe(
      catchError((error: HttpErrorResponse) => {
        const data = {
          reason: error && error.error.title ? error.error.title : '',
          status: error.status
        };

        return throwError(data);
      })
    );
  }

}
