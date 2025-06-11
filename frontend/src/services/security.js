// Security helper service
import { ref } from 'vue';

// Constants
const MAX_LOGIN_ATTEMPTS = 5;
const LOCKOUT_DURATION = 15 * 60 * 1000; // 15 minutes

class SecurityHelper {
  constructor() {
    this.loginAttempts = ref(parseInt(localStorage.getItem('loginAttempts') || '0'));
    this.lockoutUntil = ref(parseInt(localStorage.getItem('lockoutUntil') || '0'));
  }

  isAccountLocked() {
    if (!this.lockoutUntil.value) return false;
    if (Date.now() >= this.lockoutUntil.value) {
      this.resetLockout();
      return false;
    }
    return true;
  }

  getRemainingLockoutTime() {
    if (!this.lockoutUntil.value) return 0;
    const remaining = this.lockoutUntil.value - Date.now();
    return remaining > 0 ? remaining : 0;
  }

  incrementLoginAttempts() {
    this.loginAttempts.value++;
    if (this.loginAttempts.value >= MAX_LOGIN_ATTEMPTS) {
      this.lockoutUntil.value = Date.now() + LOCKOUT_DURATION;
      localStorage.setItem('lockoutUntil', this.lockoutUntil.value.toString());
    }
  }

  resetLockout() {
    this.loginAttempts.value = 0;
    this.lockoutUntil.value = null;
    localStorage.removeItem('lockoutUntil');
  }

  validatePassword(password) {
    const minLength = 8;
    const hasUpperCase = /[A-Z]/.test(password);
    const hasLowerCase = /[a-z]/.test(password);
    const hasNumbers = /\d/.test(password);
    const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);

    const errors = [];

    if (password.length < minLength) {
      errors.push(`Password must be at least ${minLength} characters long`);
    }
    if (!hasUpperCase) {
      errors.push('Password must contain at least one uppercase letter');
    }
    if (!hasLowerCase) {
      errors.push('Password must contain at least one lowercase letter');
    }
    if (!hasNumbers) {
      errors.push('Password must contain at least one number');
    }
    if (!hasSpecialChar) {
      errors.push('Password must contain at least one special character');
    }

    return {
      isValid: errors.length === 0,
      errors
    };
  }

  sanitizeInput(input) {
    if (typeof input !== 'string') return input;
    return input
      .replace(/[<>]/g, '') // Remove potential HTML tags
      .trim();
  }

  validateEmail(email) {
    const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    return emailRegex.test(email);
  }
}

// Create a factory function instead of a singleton
export const createSecurityHelper = () => new SecurityHelper();
