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
import { InstallationListComponent } from './installation-list/installation-list.component';
import { HomeComponent } from './home/home.component';
import { InstallationAddComponent } from './installation-add/installation-add.component';
import { AccountComponent } from './account/account.component';
import { MonitoringComponent } from './monitoring/monitoring.component';
import { NgxChartsModule } from '@swimlane/ngx-charts';
import { InstallationDetailComponent } from './installation-detail/installation-detail.component';
import { RequestInterceptor } from './interceptors/request.interceptor';
import { NavbarMenuComponent } from './navbar-menu/navbar-menu.component';
import { LogoutComponent } from './logout/logout.component';
import { PackageComponent } from './package/package.component';
import { PackageOverviewSheetComponent } from './package/package-overview-sheet.component';
import { ConsoleOutputComponent } from './console-output/console-output.component';
import { MonitoringDialogComponent } from './monitoring-dialog/monitoring-dialog.component';

registerLocaleData(localeDe);

@NgModule({
  declarations: [
    AppComponent,
    HomeComponent,
    LoginComponent,
    NavbarComponent,
    InstallationListComponent,
    InstallationAddComponent,
    AccountComponent,
    MonitoringComponent,
    InstallationDetailComponent,
    NavbarMenuComponent,
    LogoutComponent,
    PackageComponent,
    PackageOverviewSheetComponent,
    ConsoleOutputComponent,
    MonitoringDialogComponent,
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
    MonitoringComponent,
    MonitoringDialogComponent,
    PackageOverviewSheetComponent,
    ConsoleOutputComponent,
  ]
})
export class AppModule {
}
