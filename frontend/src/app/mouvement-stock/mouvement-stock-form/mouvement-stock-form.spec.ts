import { ComponentFixture, TestBed } from '@angular/core/testing';

import { MouvementStockForm } from './mouvement-stock-form';

describe('MouvementStockForm', () => {
  let component: MouvementStockForm;
  let fixture: ComponentFixture<MouvementStockForm>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [MouvementStockForm]
    })
    .compileComponents();

    fixture = TestBed.createComponent(MouvementStockForm);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
