import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { LoginComponent } from './login/login.component';
import { HomeComponent } from './home/home.component';
import { WebsiteListComponent } from './website-list/website-list.component';
import { WebsiteAddComponent } from './website-list/website-add/website-add.component';

const routes: Routes = [
  { path: '', component: HomeComponent },
  { path: 'websites', component: WebsiteListComponent },
  { path: 'website/add', component: WebsiteAddComponent },
  { path: 'login', component: LoginComponent }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule {
}
