import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CategorieForm } from './categorie-form';

describe('CategorieForm', () => {
  let component: CategorieForm;
  let fixture: ComponentFixture<CategorieForm>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [CategorieForm]
    })
    .compileComponents();

    fixture = TestBed.createComponent(CategorieForm);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
