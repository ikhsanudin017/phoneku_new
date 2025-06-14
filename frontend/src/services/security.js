// Security Helper Service
const LOCKOUT_DURATION = 15 * 60 * 1000 // 15 minutes in milliseconds
const MAX_LOGIN_ATTEMPTS = 5

export const createSecurityHelper = () => {
  // Get stored values
  const getStoredAttempts = () => {
    const stored = localStorage.getItem('loginAttempts')
    return stored ? parseInt(stored) : 0
  }

  const getLockedUntil = () => {
    const stored = localStorage.getItem('lockedUntil')
    return stored ? parseInt(stored) : 0
  }

  // Store values
  const storeAttempts = (attempts) => {
    localStorage.setItem('loginAttempts', attempts.toString())
  }

  const storeLockTime = (timestamp) => {
    localStorage.setItem('lockedUntil', timestamp.toString())
  }

  return {
    // Input sanitization
    sanitizeInput(input) {
      if (typeof input !== 'string') return ''
      return input.trim()
        .replace(/[<>]/g, '') // Remove potential HTML/XML tags
        .replace(/^\s+|\s+$/g, '') // Trim whitespace
    },

    // Email validation
    validateEmail(email) {
      const re = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/
      return re.test(String(email).toLowerCase())
    },

    // Login attempts management
    incrementLoginAttempts() {
      const attempts = getStoredAttempts() + 1
      storeAttempts(attempts)
      
      if (attempts >= MAX_LOGIN_ATTEMPTS) {
        storeLockTime(Date.now() + LOCKOUT_DURATION)
      }

      return attempts
    },

    resetLoginAttempts() {
      localStorage.removeItem('loginAttempts')
      localStorage.removeItem('lockedUntil')
    },

    // Account locking
    isAccountLocked() {
      const lockedUntil = getLockedUntil()
      if (lockedUntil > Date.now()) {
        return true
      }
      // Clear old lockout if expired
      if (lockedUntil > 0) {
        this.resetLoginAttempts()
      }
      return false
    },

    getRemainingLockoutTime() {
      const lockedUntil = getLockedUntil()
      const now = Date.now()
      return Math.max(0, lockedUntil - now)
    },

    shouldLockAccount() {
      return getStoredAttempts() >= MAX_LOGIN_ATTEMPTS
    },

    lockAccount() {
      storeLockTime(Date.now() + LOCKOUT_DURATION)
    },

    // Get current attempts count
    getCurrentAttempts() {
      return getStoredAttempts()
    }
  }
}
