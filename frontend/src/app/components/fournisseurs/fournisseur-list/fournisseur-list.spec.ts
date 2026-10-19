import { ComponentFixture, TestBed } from '@angular/core/testing';
import { FournisseurListComponent } from './fournisseur-list';
import { HttpClientTestingModule } from '@angular/common/http/testing';
import { RouterTestingModule } from '@angular/router/testing';

describe('FournisseurListComponent', () => {
  let component: FournisseurListComponent;
  let fixture: ComponentFixture<FournisseurListComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [
        FournisseurListComponent,
        HttpClientTestingModule,
        RouterTestingModule
      ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(FournisseurListComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});