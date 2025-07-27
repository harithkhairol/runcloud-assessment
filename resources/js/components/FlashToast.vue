<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()

const visible = ref(false)
const flash = computed(() => page.props.flash as { success?: string; error?: string })

watch(
  () => flash.value,
  (newFlash) => {
    if (newFlash?.success || newFlash?.error) {
      visible.value = true
      setTimeout(() => {
        visible.value = false
      }, 4000)
    }
  },
  { immediate: true, deep: true }
)
</script>

<template>
  <transition name="fade">
    <div
      v-if="visible"
      class="fixed top-4 right-4 z-50 rounded bg-green-100 text-green-800 px-4 py-2 shadow-lg"
    >
      <p v-if="flash.success">{{ flash.success }}</p>
      <p v-else-if="flash.error" class="text-red-600">{{ flash.error }}</p>
    </div>
  </transition>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
