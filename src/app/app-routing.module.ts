import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { LoginComponent } from './login/login.component';
import { HomeComponent } from './home/home.component';
import { WebsiteComponent } from './website/website.component';
import { WebsiteListComponent } from './website/website-list/website-list.component';
import { WebsiteDetailComponent } from './website/website-detail/website-detail.component';
import { WebsiteAddComponent } from './website/website-add/website-add.component';

const routes: Routes = [
  { path: '', component: HomeComponent },
  {
    path: 'websites', component: WebsiteComponent, children: [
      { path: '', component: WebsiteListComponent },
      { path: 'website/:name', component: WebsiteDetailComponent },
      { path: 'add', component: WebsiteAddComponent },
    ]
  },
  { path: 'login', component: LoginComponent }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule {
}
