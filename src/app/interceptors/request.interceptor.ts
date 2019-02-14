import { HttpErrorResponse, HttpEvent, HttpHandler, HttpInterceptor, HttpRequest } from '@angular/common/http';
import { Observable, throwError } from 'rxjs';
import { catchError } from 'rxjs/operators';
import { AuthenticationService } from '../services/authentication.service';
import { Injectable } from '@angular/core';

@Injectable({
  providedIn: 'root'
})
export class RequestInterceptor implements HttpInterceptor {

  constructor(private auth: AuthenticationService) {
  }

  // sets token, content type and accept headers.
  // error handling in catchError statement
  intercept(req: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    if (this.auth.getToken()) {
      if (this.auth.getTimeUntilLogout() < 300) {
        this.auth.refreshToken();
      }
      req = req.clone({ headers: req.headers.set('Authorization', 'Bearer ' + this.auth.getToken()) });
    }

    if (!req.headers.has('Content-Type')) {
      req = req.clone({ headers: req.headers.set('Content-Type', 'application/json') });
    }
    req = req.clone({ headers: req.headers.set('Accept', 'application/json') });

    return next.handle(req).pipe(
      catchError((error: HttpErrorResponse) => {
        const data = {
          reason: error && error.error.title ? error.error.title : '',
          status: error.status
        };
        console.log(data);

        return throwError(error);
      })
    );

  }

}
