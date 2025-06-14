import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import { initializeCsrf } from './services/api'

import './style.css'
import '@fortawesome/fontawesome-free/css/all.css'

const app = createApp(App)
const pinia = createPinia()

// Initialize Pinia
app.use(pinia)

// Initialize router
app.use(router)

// Error handling for Pinia
pinia.use(({ store }) => {
  store.$onAction(({ name, args, after, onError }) => {
    after((result) => {
      // Handle successful actions
      console.debug(`[🔵 ${store.$id}] Successfully completed ${name}.`)
    })
    onError((error) => {
      // Handle action errors
      console.error(`[🔴 ${store.$id}] Error in ${name}:`, error)
    })
  })
})

// Initialize CSRF protection before mounting
initializeCsrf().then(() => {
  console.log('CSRF token initialized, mounting app...')
  app.mount('#app')
}).catch(error => {
  console.error('Failed to initialize CSRF token:', error)
  // Mount app anyway but show error notification
  app.mount('#app')
})
