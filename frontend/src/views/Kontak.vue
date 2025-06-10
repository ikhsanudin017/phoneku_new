<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 pt-16">
    <!-- Navbar -->
    <NavbarComponent class="z-20 fixed top-0 w-full" />

    <!-- Hero Section -->
    <header class="relative bg-gradient-to-r from-blue-600 to-blue-500 pb-32 pt-28">
      <div class="absolute inset-0 bg-blue-600/20 backdrop-blur-sm"></div>
      <div class="container relative mx-auto px-6">
        <div class="max-w-3xl mx-auto text-center">
          <h1 class="text-4xl md:text-5xl font-bold text-white mb-6">
            Kontak Kami
          </h1>
          <p class="text-xl text-blue-100 max-w-2xl mx-auto">
            Kami siap membantu Anda dengan pertanyaan, saran, atau keluhan yang Anda miliki
          </p>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="relative -mt-32 pb-16">
      <div class="container mx-auto px-6">
        <!-- Contact Section -->
        <div class="bg-white rounded-2xl shadow-2xl p-8 mb-12">
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
            <!-- Contact Information -->
            <section class="space-y-10">
              <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Hubungi Kami</h2>
                <p class="text-lg text-gray-600 leading-relaxed">
                  Kami selalu siap membantu dan menjawab pertanyaan Anda. Silakan hubungi kami melalui salah satu channel di bawah ini.
                </p>
              </div>

              <!-- Contact Cards -->
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Call Center -->
                <div class="bg-gray-50 rounded-xl p-6 hover:bg-blue-50 transition-all duration-300 group">
                  <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-blue-200 transition-colors">
                    <i class="fas fa-phone text-blue-600 text-xl"></i>
                  </div>
                  <h3 class="text-lg font-semibold text-gray-900 mb-3">Pusat Panggilan</h3>
                  <a href="tel:08-311-328-913-799" class="inline-flex text-blue-600 hover:text-blue-700 font-medium group-hover:translate-x-1 transition-transform">
                    08-311-328-913-799
                  </a>
                </div>

                <!-- Email -->
                <div class="bg-gray-50 rounded-xl p-6 hover:bg-blue-50 transition-all duration-300 group">
                  <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-blue-200 transition-colors">
                    <i class="fas fa-envelope text-blue-600 text-xl"></i>
                  </div>
                  <h3 class="text-lg font-semibold text-gray-900 mb-3">Email</h3>
                  <a href="mailto:phonku@mail.com" class="inline-flex text-blue-600 hover:text-blue-700 font-medium group-hover:translate-x-1 transition-transform">
                    phonku@mail.com
                  </a>
                </div>

                <!-- Location -->
                <div class="bg-gray-50 rounded-xl p-6 hover:bg-blue-50 transition-all duration-300 group">
                  <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-blue-200 transition-colors">
                    <i class="fas fa-map-marker-alt text-blue-600 text-xl"></i>
                  </div>
                  <h3 class="text-lg font-semibold text-gray-900 mb-3">Lokasi</h3>
                  <a href="https://maps.app.goo.gl/TDxKt3buY2CinubM7" target="_blank" class="inline-flex text-blue-600 hover:text-blue-700 font-medium group-hover:translate-x-1 transition-transform">
                    Universitas Amikom Yogyakarta
                  </a>
                </div>

                <!-- Social Media -->
                <div class="bg-gray-50 rounded-xl p-6 hover:bg-blue-50 transition-all duration-300 group">
                  <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-blue-200 transition-colors">
                    <i class="fas fa-share-alt text-blue-600 text-xl"></i>
                  </div>
                  <h3 class="text-lg font-semibold text-gray-900 mb-3">Media Sosial</h3>
                  <div class="flex space-x-5">
                    <a href="#" class="text-gray-500 hover:text-blue-600 transition-colors transform hover:scale-110">
                      <i class="fab fa-twitter text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-500 hover:text-blue-600 transition-colors transform hover:scale-110">
                      <i class="fab fa-facebook text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-500 hover:text-blue-600 transition-colors transform hover:scale-110">
                      <i class="fab fa-instagram text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-500 hover:text-blue-600 transition-colors transform hover:scale-110">
                      <i class="fab fa-tiktok text-xl"></i>
                    </a>
                  </div>
                </div>
              </div>
            </section>

            <!-- Contact Form -->
            <section>
              <div class="bg-gradient-to-br from-blue-600 to-blue-500 rounded-2xl p-8 shadow-xl">
                <h3 class="text-2xl font-bold text-white mb-3">Kirim Pesan</h3>
                <p class="text-blue-100 text-lg mb-8">
                  Tim kami akan merespons pesan Anda secepat mungkin
                </p>

                <form @submit.prevent="handleSubmit" class="space-y-6">
                  <!-- Name Input -->
                  <div class="relative">
                    <input
                      v-model="form.name"
                      type="text"
                      required
                      class="w-full px-5 py-4 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/60 focus:outline-none focus:border-white/40 transition-colors"
                      placeholder="Nama Lengkap"
                    />
                  </div>

                  <!-- Email Input -->
                  <div class="relative">
                    <input
                      v-model="form.email"
                      type="email"
                      required
                      class="w-full px-5 py-4 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/60 focus:outline-none focus:border-white/40 transition-colors"
                      placeholder="Email"
                    />
                  </div>

                  <!-- Subject Input -->
                  <div class="relative">
                    <input
                      v-model="form.subject"
                      type="text"
                      required
                      class="w-full px-5 py-4 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/60 focus:outline-none focus:border-white/40 transition-colors"
                      placeholder="Subjek"
                    />
                  </div>

                  <!-- Message Input -->
                  <div class="relative">
                    <textarea
                      v-model="form.message"
                      required
                      rows="4"
                      class="w-full px-5 py-4 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/60 focus:outline-none focus:border-white/40 transition-colors resize-none"
                      placeholder="Pesan"
                    ></textarea>
                  </div>

                  <!-- Success/Error Messages -->
                  <TransitionGroup name="fade">
                    <div v-if="success" key="success" class="bg-green-500/20 border border-green-500/30 text-white rounded-xl px-5 py-4">
                      {{ success }}
                    </div>
                    <div v-if="error" key="error" class="bg-red-500/20 border border-red-500/30 text-white rounded-xl px-5 py-4">
                      {{ error }}
                    </div>
                  </TransitionGroup>

                  <!-- Submit Button -->
                  <button
                    type="submit"
                    :disabled="loading"
                    class="w-full bg-white text-blue-600 font-semibold px-8 py-4 rounded-xl hover:bg-blue-50 disabled:opacity-70 disabled:cursor-not-allowed transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center space-x-3"
                  >
                    <div v-if="loading" class="animate-spin rounded-full h-5 w-5 border-2 border-blue-600 border-t-transparent"></div>
                    <span class="text-lg">{{ loading ? 'Mengirim...' : 'Kirim Pesan' }}</span>
                  </button>
                </form>
              </div>
            </section>
          </div>
        </div>

        <!-- Map Section -->
        <section class="bg-white rounded-2xl shadow-2xl overflow-hidden">
          <div class="p-8">
            <h3 class="text-2xl font-bold text-gray-900 mb-6">Lokasi Kami</h3>
            <div class="aspect-video rounded-xl overflow-hidden">
              <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3953.2818402780804!2d110.4090461!3d-7.7599048999999996!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a599bd3bdc4ef%3A0x6f1714b0c4544586!2sUniversitas%20Amikom%20Yogyakarta!5e0!3m2!1sid!2sid!4v1744115106486!5m2!1sid!2sid"
                class="w-full h-full border-0"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
              </iframe>
            </div>
          </div>
        </section>
      </div>
    </main>

    <!-- Footer -->
    <FooterComponent />
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useAuthStore } from '@/stores/auth'
import NavbarComponent from '@/components/NavbarComponent.vue'
import FooterComponent from '@/components/FooterComponent.vue'

// Store initialization
const authStore = useAuthStore()

// Form state
const loading = ref(false)
const success = ref('')
const error = ref('')

// Form data
const form = reactive({
  name: '',
  email: '',
  subject: '',
  message: ''
})

// Form submission handler
const handleSubmit = async () => {
  loading.value = true
  error.value = ''
  success.value = ''

  try {
    // TODO: Replace with actual API call
    await new Promise(resolve => setTimeout(resolve, 1500))

    // Show success message
    success.value = 'Pesan Anda telah berhasil dikirim! Tim kami akan merespons dalam 24 jam.'

    // Reset form
    Object.keys(form).forEach(key => form[key] = '')
  } catch (err) {
    console.error('Contact form error:', err)
    error.value = 'Terjadi kesalahan saat mengirim pesan. Silakan coba lagi.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: all 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

/* Custom scrollbar for textareas */
textarea::-webkit-scrollbar {
  width: 8px;
}

textarea::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 4px;
}

textarea::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.2);
  border-radius: 4px;
}

textarea::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.3);
}

/* Improve form input autofill styling */
input:-webkit-autofill,
input:-webkit-autofill:hover,
input:-webkit-autofill:focus,
input:-webkit-autofill:active {
  -webkit-background-clip: text;
  -webkit-text-fill-color: white;
  transition: background-color 5000s ease-in-out 0s;
  box-shadow: inset 0 0 20px 20px rgba(255, 255, 255, 0.1);
}
</style>
