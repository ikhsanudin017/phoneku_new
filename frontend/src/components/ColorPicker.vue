<template>
  <div class="color-picker">
    <div class="flex flex-wrap gap-2 mb-2">
      <div v-for="(color, index) in modelValue"
           :key="color"
           class="inline-flex items-center space-x-2 bg-gray-100 p-2 rounded">
        <div class="w-6 h-6 rounded border border-gray-300"
             :style="{ backgroundColor: color }"></div>
        <span class="text-sm">{{ getColorName(color) }}</span>
        <button type="button" @click="removeColor(index)"
                class="text-red-500 hover:text-red-700">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
      <div class="inline-flex items-center">
        <div ref="pickerContainer" class="color-picker-button"></div>
        <span class="ml-2 text-sm text-gray-600">Click to add color</span>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import '@simonwep/pickr/dist/themes/classic.min.css'
import Pickr from '@simonwep/pickr'

export default {
  name: 'ColorPicker',
  props: {
    modelValue: {
      type: Array,
      required: true
    }
  },
  emits: ['update:modelValue'],

  setup(props, { emit }) {
    const pickerContainer = ref(null)
    const picker = ref(null)

    const colorNameMap = {
      '#FF0000': 'Merah',
      '#00FF00': 'Hijau',
      '#0000FF': 'Biru',
      '#FFFF00': 'Kuning',
      '#FF00FF': 'Magenta',
      '#00FFFF': 'Cyan',
      '#000000': 'Hitam',
      '#FFFFFF': 'Putih',
      '#808080': 'Abu-abu',
      '#C0C0C0': 'Silver'
    }

    const getColorName = (color) => {
      return colorNameMap[color.toUpperCase()] || color
    }

    const removeColor = (index) => {
      const newColors = [...props.modelValue]
      newColors.splice(index, 1)
      emit('update:modelValue', newColors)
    }

    onMounted(() => {
      picker.value = Pickr.create({
        el: pickerContainer.value,
        theme: 'classic',
        default: '#000000',
        swatches: Object.keys(colorNameMap),
        components: {
          preview: true,
          opacity: false,
          hue: true,
          interaction: {
            hex: true,
            rgba: true,
            hsla: false,
            hsva: false,
            cmyk: false,
            input: true,
            clear: false,
            save: true
          }
        }
      })

      picker.value.on('save', (color) => {
        if (!color) return
        const hexColor = color.toHEXA().toString().toUpperCase()
        if (!props.modelValue.includes(hexColor)) {
          emit('update:modelValue', [...props.modelValue, hexColor])
        }
        picker.value.hide()
      })
    })

    onBeforeUnmount(() => {
      if (picker.value) {
        picker.value.destroyAndRemove()
      }
    })

    return {
      pickerContainer,
      getColorName,
      removeColor
    }
  }
}
</script>

<style>
.color-picker-button {
  display: inline-block;
}

.pickr {
  width: 2rem !important;
  height: 2rem !important;
  border-radius: 0.375rem;
  overflow: hidden;
}

.pickr button {
  border-radius: 0.375rem !important;
}
</style>
