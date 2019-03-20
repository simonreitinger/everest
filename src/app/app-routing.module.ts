import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { LoginComponent } from './login/login.component';
import { AuthGuard } from './guards/auth.guard';
import { LogoutComponent } from './logout/logout.component';
import { AccountComponent } from './account/account.component';
import { InstallationListComponent } from './installation-list/installation-list.component';
import { InstallationAddComponent } from './installation-add/installation-add.component';
import { InstallationDetailComponent } from './installation-detail/installation-detail.component';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { InstallationRegisterComponent } from './installation-register/installation-register.component';

const routes: Routes = [
  { path: '', redirectTo: 'login', pathMatch: 'full' },
  { path: 'login', component: LoginComponent },
  { path: 'logout', component: LogoutComponent },
  { path: 'installations', component: InstallationListComponent, canActivate: [AuthGuard] },
  { path: 'installations/register', component: InstallationRegisterComponent, canActivate: [AuthGuard] },
  { path: 'installation/:hash', component: InstallationDetailComponent, canActivate: [AuthGuard] },
  { path: 'account', component: AccountComponent, canActivate: [AuthGuard] }
];

@NgModule({
  imports: [RouterModule.forRoot(routes), FormsModule, ReactiveFormsModule],
  exports: [RouterModule]
})
export class AppRoutingModule {
}
