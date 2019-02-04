import { Component, OnInit } from '@angular/core';
import { FormControl, Validators } from '@angular/forms';
import { AuthenticationService } from '../services/authentication.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss']
})
export class LoginComponent implements OnInit {
  username = new FormControl('', [Validators.required]);
  password = new FormControl('', [Validators.required]);

  constructor(private auth: AuthenticationService, private router: Router) {
  }

  ngOnInit() {
  }

  onLogin() {
    this.auth.login(this.username.value, this.password.value).subscribe(success => {
      if (success) {
        this.router.navigateByUrl('/websites');
      }
    });
  }

}
