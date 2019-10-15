import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { UserService } from '../services/user.service';
import { UserModel } from '../models/user.model';
import { NgForm } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthenticationService } from '../services/authentication.service';
import { MatDialog, MatFormField } from '@angular/material';

@Component({
  selector: 'app-account',
  templateUrl: './account.component.html',
  styleUrls: ['./account.component.scss']
})
export class AccountComponent implements OnInit {

  canSubmit = true;

  user: UserModel;
  initialUsername: string;
  usernames: string[];

  @ViewChild('container') container;

  constructor(private auth: AuthenticationService, private us: UserService, private router: Router, private dialog: MatDialog) {
  }

  ngOnInit() {
    this.us.getUser().subscribe(res => {
      this.user = res;
      this.initialUsername = res.username;
    });

    this.us.getUsers().subscribe(res => {
      this.usernames = res;
    });
  }

  checkUsername() {
    const currentInput = this.user.username;

    if (this.usernames.includes(currentInput) && currentInput !== this.initialUsername) {
      this.canSubmit = false;
      return;
    }

    this.canSubmit = true;
  }

  onSubmit(form: NgForm) {
    // form has been edited
    if (form.dirty) {
      this.us.updateUserData(form.value).subscribe(res => {
        this.user = res;
        this.usernames = this.usernames.filter(username => username !== this.initialUsername);
        this.initialUsername = res.username;

        if (this.initialUsername !== res.username) {
          this.dialog.open(this.container);
          this.auth.logout();
        } else {
          this.router.navigateByUrl('/installations');
        }
      });


    }

  }
}
