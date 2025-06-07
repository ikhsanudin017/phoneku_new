<template>
  <div class="fixed inset-0 flex items-center justify-center z-50">
    <div class="absolute inset-0 bg-black bg-opacity-50" @click="$emit('close')"></div>
    <div class="modal-content relative bg-white rounded-lg shadow-lg w-full max-w-md mx-auto p-6 z-10 animate-fadeIn">
      <button
        type="button"
        class="absolute top-3 right-3 text-gray-400 hover:text-blue-500 text-2xl font-bold"
        @click="$emit('close')"
      >&times;</button>

      <h2 class="text-xl font-bold mb-4">Edit Alamat</h2>

      <form @submit.prevent="saveAddress" class="space-y-4">
        <div>
          <label for="label" class="block text-gray-700 mb-2">Label Alamat (Rumah/Kantor/dll)</label>
          <input
            type="text"
            id="label"
            v-model="formData.label"
            class="w-full border rounded p-2"
            required
          >
        </div>

        <div>
          <label for="recipient_name" class="block text-gray-700 mb-2">Nama Penerima</label>
          <input
            type="text"
            id="recipient_name"
            v-model="formData.recipientName"
            class="w-full border rounded p-2"
            required
          >
        </div>

        <div>
          <label for="phone" class="block text-gray-700 mb-2">Nomor Telepon</label>
          <input
            type="tel"
            id="phone"
            v-model="formData.phone"
            class="w-full border rounded p-2"
            required
          >
        </div>

        <div>
          <label for="address" class="block text-gray-700 mb-2">Alamat Lengkap</label>
          <textarea
            id="address"
            v-model="formData.fullAddress"
            rows="3"
            class="w-full border rounded p-2"
            required
          ></textarea>
          <button
            type="button"
            class="mt-2 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
            @click="showMap = true"
          >
            Pilih Lokasi di Peta
          </button>
        </div>

        <div class="flex justify-end gap-2 mt-4">
          <button
            type="button"
            class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300"
            @click="$emit('close')"
          >
            Batal
          </button>
          <button
            type="submit"
            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 font-semibold"
          >
            Simpan
          </button>
        </div>
      </form>

      <!-- Map Modal -->
      <div v-if="showMap" class="fixed inset-0 flex items-center justify-center z-60">
        <div class="absolute inset-0 bg-black bg-opacity-50" @click="showMap = false"></div>
        <div class="relative bg-white rounded-lg shadow-lg w-full max-w-lg mx-auto p-4 z-10 animate-fadeIn">
          <h3 class="text-lg font-bold mb-2">Pilih Lokasi di Peta</h3>
          <div class="mb-3 flex gap-2">
            <input
              type="text"
              v-model="searchQuery"
              class="flex-1 border rounded p-2"
              placeholder="Cari alamat atau tempat..."
            >
            <button
              type="button"
              class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
              @click="searchLocation"
            >
              Cari
            </button>
          </div>
          <div ref="mapContainer" class="h-[300px] rounded-lg"></div>
          <div class="flex justify-end gap-2 mt-4">
            <button
              type="button"
              class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300"
              @click="showMap = false"
            >
              Batal
            </button>
            <button
              type="button"
              class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 font-semibold"
              @click="useSelectedLocation"
            >
              Gunakan Lokasi Ini
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'

const props = defineProps({
  address: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['close', 'save'])

const showMap = ref(false)
const searchQuery = ref('')
const mapContainer = ref(null)
let map = null
let marker = null

const formData = reactive({
  label: props.address.label || 'Rumah',
  recipientName: props.address.recipientName || '',
  phone: props.address.phone || '',
  fullAddress: props.address.fullAddress || '',
  latitude: props.address.latitude || '',
  longitude: props.address.longitude || ''
})

const initMap = () => {
  if (!map && mapContainer.value) {
    map = L.map(mapContainer.value).setView([-7.797068, 110.370529], 13)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19
    }).addTo(map)
    marker = L.marker([-7.797068, 110.370529], { draggable: true }).addTo(map)

    map.on('click', (e) => {
      marker.setLatLng(e.latlng)
    })

    // Try to get user's location
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition((pos) => {
        const latlng = [pos.coords.latitude, pos.coords.longitude]
        map.setView(latlng, 16)
        marker.setLatLng(latlng)
      })
    }
  }
}

const searchLocation = async () => {
  if (!searchQuery.value) return

  try {
    const response = await fetch(
      `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(searchQuery.value)}`
    )
    const data = await response.json()

    if (data && data.length > 0) {
      const lat = parseFloat(data[0].lat)
      const lon = parseFloat(data[0].lon)
      map.setView([lat, lon], 16)
      marker.setLatLng([lat, lon])
    } else {
      alert('Lokasi tidak ditemukan!')
    }
  } catch (error) {
    console.error('Error searching location:', error)
    alert('Gagal mencari lokasi')
  }
}

const useSelectedLocation = async () => {
  const latlng = marker.getLatLng()
  formData.latitude = latlng.lat
  formData.longitude = latlng.lng

  try {
    const response = await fetch(
      `https://nominatim.openstreetmap.org/reverse?format=json&lat=${latlng.lat}&lon=${latlng.lng}`
    )
    const data = await response.json()
    formData.fullAddress = data.display_name
    showMap.value = false
  } catch (error) {
    console.error('Error getting address:', error)
    alert('Gagal mendapatkan alamat')
  }
}

const saveAddress = () => {
  emit('save', { ...formData })
}

watch(showMap, (newValue) => {
  if (newValue) {
    nextTick(() => {
      initMap()
    })
  }
})
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.98); }
  to { opacity: 1; transform: scale(1); }
}
.animate-fadeIn {
  animation: fadeIn 0.2s;
}
</style>
