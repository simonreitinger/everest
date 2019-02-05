import { Component, OnInit } from '@angular/core';
import { AuthenticationService } from '../services/authentication.service';
import { Router } from '@angular/router';
import { UserService } from '../services/user.service';
import { UserModel } from '../models/user.model';

@Component({
  selector: 'app-navbar-menu',
  templateUrl: './navbar-menu.component.html',
  styleUrls: ['./navbar-menu.component.scss']
})
export class NavbarMenuComponent implements OnInit {

  user: UserModel;

  constructor(private auth: AuthenticationService, private us: UserService, private router: Router) { }

  ngOnInit() {
    this.us.getUser().subscribe(res => {
      this.user = res;
    });
  }

  logout() {
    this.auth.logout();
    this.router.navigateByUrl('/');
  }
}
