import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { LoginComponent } from './login/login.component';
import { HomeComponent } from './home/home.component';
import { WebsiteListComponent } from './website-list/website-list.component';
import { WebsiteAddComponent } from './website-add/website-add.component';
import { WebsiteDetailComponent } from './website-detail/website-detail.component';
import { AuthGuard } from './guards/auth.guard';

const routes: Routes = [
  { path: '', component: HomeComponent },
  { path: 'websites', component: WebsiteListComponent, canActivate: [AuthGuard] },
  { path: 'website/add', component: WebsiteAddComponent, canActivate: [AuthGuard] },
  { path: 'website/detail/:name', component: WebsiteDetailComponent, canActivate: [AuthGuard] },
  { path: 'login', component: LoginComponent }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule {
}
