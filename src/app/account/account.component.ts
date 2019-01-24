import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-account',
  templateUrl: './account.component.html',
  styleUrls: ['./account.component.scss']
})
export class AccountComponent implements OnInit {

  show: boolean = false;

  constructor() { }

  ngOnInit() {
  }

  showDropdown() {
    this.show = !this.show;
  }
}
