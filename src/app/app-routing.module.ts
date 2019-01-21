import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { LoginComponent } from './login/login.component';
import { HomeComponent } from './home/home.component';
import { WebsiteComponent } from './website/website.component';
import { WebsiteAddComponent } from './website/website-add/website-add.component';

const routes: Routes = [
  { path: '', component: HomeComponent },
  { path: 'websites', component: WebsiteComponent },
  { path: 'website/add', component: WebsiteAddComponent },
  { path: 'login', component: LoginComponent }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule {
}
