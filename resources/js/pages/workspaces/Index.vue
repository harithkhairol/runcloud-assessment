<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import type { BreadcrumbItem } from '@/types'

const props = defineProps<{ workspaces: { id: number; name: string }[] }>()

import FlashToast from '@/components/FlashToast.vue'

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Workspaces', href: '/workspaces' },
]

const name = ref('')
const submitting = ref(false)

const submit = () => {
  submitting.value = true
  router.post('/workspaces', { name: name.value }, {
    onSuccess: () => {
      name.value = ''
    },
    onFinish: () => {
      submitting.value = false
    }
  })
}
</script>

<template>
  <Head title="Workspaces" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4 space-y-4">

      <FlashToast />

      <h1 class="text-2xl font-bold">Workspaces</h1>

      <form @submit.prevent="submit" class="mt-6 flex gap-2 items-center">
        
            <input
            v-model="name"
            type="text"
            placeholder="New workspace name"
            class="border border-gray-300 dark:border-gray-700 px-3 py-2 rounded w-full"
            />
            <button
            type="submit"
            class="bg-blue-600 text-white px-4 py-2 rounded"
            :disabled="submitting"
            >
            {{ submitting ? 'Creating...' : 'Create' }}
            </button>
        </form>

      <ul class="space-y-2">
        <li
          v-for="ws in workspaces"
          :key="ws.id"
          class="border p-4 rounded bg-white dark:bg-sidebar"
        >
          <a :href="route('workspaces.show', ws.id)" class="text-blue-500 hover:underline">
            {{ ws.name }}
          </a>
        </li>
      </ul>

    </div>
  </AppLayout>
</template>

