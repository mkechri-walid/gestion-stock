import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AlerteList } from './alerte-list';

describe('AlerteList', () => {
  let component: AlerteList;
  let fixture: ComponentFixture<AlerteList>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [AlerteList]
    })
    .compileComponents();

    fixture = TestBed.createComponent(AlerteList);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
