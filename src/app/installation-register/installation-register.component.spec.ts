import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { InstallationRegisterComponent } from './installation-register.component';

describe('InstallationRegisterComponent', () => {
  let component: InstallationRegisterComponent;
  let fixture: ComponentFixture<InstallationRegisterComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ InstallationRegisterComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(InstallationRegisterComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
