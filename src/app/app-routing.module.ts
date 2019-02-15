import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { LoginComponent } from './login/login.component';
import { HomeComponent } from './home/home.component';
import { AuthGuard } from './guards/auth.guard';
import { LogoutComponent } from './logout/logout.component';
import { AccountComponent } from './account/account.component';
import { InstallationListComponent } from './installation-list/installation-list.component';
import { InstallationAddComponent } from './installation-add/installation-add.component';
import { InstallationDetailComponent } from './installation-detail/installation-detail.component';

const routes: Routes = [
  { path: '', component: HomeComponent },
  { path: 'login', component: LoginComponent },
  { path: 'logout', component: LogoutComponent },
  { path: 'installations', component: InstallationListComponent, canActivate: [AuthGuard] },
  { path: 'installations/add', component: InstallationAddComponent, canActivate: [AuthGuard] },
  { path: 'installation/:hash', component: InstallationDetailComponent, canActivate: [AuthGuard] },
  { path: 'account', component: AccountComponent, canActivate: [AuthGuard] }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule {
}
