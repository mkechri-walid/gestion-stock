import { ComponentFixture, TestBed } from '@angular/core/testing';

import { MouvementStockList } from './mouvement-stock-list';

describe('MouvementStockList', () => {
  let component: MouvementStockList;
  let fixture: ComponentFixture<MouvementStockList>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [MouvementStockList]
    })
    .compileComponents();

    fixture = TestBed.createComponent(MouvementStockList);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
