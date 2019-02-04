import { BrowserModule } from '@angular/platform-browser';
import { LOCALE_ID, NgModule } from '@angular/core';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { HTTP_INTERCEPTORS, HttpClientModule } from '@angular/common/http';
import localeDe from '@angular/common/locales/de';
import { registerLocaleData } from '@angular/common';

import { AppComponent } from './app.component';
import { AppRoutingModule } from './app-routing.module';
import { MaterialModule } from './material-module';
import { NavbarComponent } from './navbar/navbar.component';
import { LoginComponent } from './login/login.component';
import { WebsiteListComponent } from './website-list/website-list.component';
import { HomeComponent } from './home/home.component';
import { WebsiteAddComponent } from './website-add/website-add.component';
import { WebsiteComponent } from './website/website.component';
import { AccountComponent } from './account/account.component';
import { MonitoringComponent } from './monitoring/monitoring.component';
import { NgxChartsModule } from '@swimlane/ngx-charts';
import { WebsiteDetailComponent } from './website-detail/website-detail.component';
import { RequestInterceptor } from './interceptors/request.interceptor';

registerLocaleData(localeDe);

@NgModule({
  declarations: [
    AppComponent,
    HomeComponent,
    LoginComponent,
    NavbarComponent,
    WebsiteListComponent,
    WebsiteAddComponent,
    WebsiteComponent,
    AccountComponent,
    MonitoringComponent,
    WebsiteDetailComponent
  ],
  imports: [
    BrowserModule,
    FormsModule,
    ReactiveFormsModule,
    AppRoutingModule,
    HttpClientModule,
    BrowserAnimationsModule,
    MaterialModule,
    NgxChartsModule
  ],
  providers: [
    { provide: LOCALE_ID, useValue: 'de' },
    { provide: HTTP_INTERCEPTORS, useClass: RequestInterceptor, multi: true }
  ],
  bootstrap: [AppComponent],
  entryComponents: [
    MonitoringComponent
  ]
})
export class AppModule {
}
