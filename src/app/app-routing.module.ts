import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { LoginComponent } from './login/login.component';
import { HomeComponent } from './home/home.component';
import { WebsiteListComponent } from './website-list/website-list.component';
import { WebsiteAddComponent } from './website-add/website-add.component';
import { WebsiteDetailComponent } from './website-detail/website-detail.component';
import { AuthGuard } from './guards/auth.guard';
import { LogoutComponent } from './logout/logout.component';
import { AccountComponent } from './account/account.component';

const routes: Routes = [
  { path: '', component: HomeComponent },
  { path: 'login', component: LoginComponent },
  { path: 'logout', component: LogoutComponent },
  { path: 'websites', component: WebsiteListComponent, canActivate: [AuthGuard] },
  { path: 'websites/add', component: WebsiteAddComponent, canActivate: [AuthGuard] },
  { path: 'website/:hash', component: WebsiteDetailComponent, canActivate: [AuthGuard] },
  { path: 'account', component: AccountComponent, canActivate: [AuthGuard] }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule {
}
