import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { NavbarComponent } from './navbar/navbar.component';
import { LoginComponent } from './login/login.component';
import { WebsiteComponent } from './website/website.component';
import { WebsiteDetailComponent } from './website/website-detail/website-detail.component';
import { WebsiteListComponent } from './website/website-list/website-list.component';
import { HomeComponent } from './home/home.component';
import { WebsiteAddComponent } from './website/website-add/website-add.component';
import { HttpClientModule } from '@angular/common/http';

@NgModule({
  declarations: [
    AppComponent,
    NavbarComponent,
    LoginComponent,
    WebsiteComponent,
    WebsiteListComponent,
    WebsiteDetailComponent,
    WebsiteDetailComponent,
    WebsiteListComponent,
    HomeComponent,
    WebsiteAddComponent
  ],
  imports: [
    BrowserModule,
    FormsModule,
    AppRoutingModule,
    HttpClientModule
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule {
}
