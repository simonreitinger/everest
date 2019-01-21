import { TestBed } from '@angular/core/testing';

import { ContaoManagerService } from './contao-manager.service';

describe('ContaoManagerService', () => {
  beforeEach(() => TestBed.configureTestingModule({}));

  it('should be created', () => {
    const service: ContaoManagerService = TestBed.get(ContaoManagerService);
    expect(service).toBeTruthy();
  });
});
