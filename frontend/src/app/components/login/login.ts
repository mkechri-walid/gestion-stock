import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './login.html',
  styleUrls: ['./login.css']
})
export class LoginComponent {
  email: string = '';
  password: string = '';
  errorMessage: string = '';

  // On injecte le Router et le AuthService
  constructor(private router: Router, private authService: AuthService) {}

  onLogin() {
    // 1. Validation simple
    if (!this.email || !this.password) {
      this.errorMessage = 'Veuillez remplir tous les champs';
      return;
    }

    // 2. Préparation des données pour Symfony
    const credentials = {
      email: this.email,
      password: this.password
    };

    // 3. Appel au backend
    this.authService.login(credentials).subscribe({
      next: (response) => {
        console.log('Connexion réussie, Token reçu !');
        // Redirection vers le dashboard
        this.router.navigate(['/dashboard']);
      },
      error: (err) => {
        console.error('Erreur login:', err);
        this.errorMessage = 'Email ou mot de passe incorrect';
      }
    });
  }
}