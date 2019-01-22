import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { NavbarComponent } from './navbar/navbar.component';
import { LoginComponent } from './login/login.component';
import { WebsiteListComponent } from './website-list/website-list.component';
import { HomeComponent } from './home/home.component';
import { WebsiteAddComponent } from './website-list/website-add/website-add.component';
import { HttpClientJsonpModule, HttpClientModule } from '@angular/common/http';
import { WebsiteComponent } from './website-list/website/website.component';

@NgModule({
  declarations: [
    AppComponent,
    HomeComponent,
    LoginComponent,
    NavbarComponent,
    WebsiteListComponent,
    WebsiteAddComponent,
    WebsiteComponent,
  ],
  imports: [
    BrowserModule,
    FormsModule,
    AppRoutingModule,
    HttpClientModule,
    HttpClientJsonpModule
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule {
}
