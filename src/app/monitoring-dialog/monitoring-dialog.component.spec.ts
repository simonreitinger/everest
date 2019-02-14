import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { MonitoringDialogComponent } from './monitoring-dialog.component';

describe('MonitoringDialogComponent', () => {
  let component: MonitoringDialogComponent;
  let fixture: ComponentFixture<MonitoringDialogComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ MonitoringDialogComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(MonitoringDialogComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
