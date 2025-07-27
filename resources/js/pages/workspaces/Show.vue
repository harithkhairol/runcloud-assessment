<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import FlashToast from '@/components/FlashToast.vue'
import type { Workspace, Task } from '@/types/types'

const props = defineProps<{
  workspace: Workspace
  tasks: Task[]
}>()

// Workspace state
const name = ref(props.workspace.name)
const editingWorkspace = ref(false)
const deletingWorkspace = ref(false)

const updateWorkspace = () => {
  router.put(`/workspaces/${props.workspace.id}`, { name: name.value }, {
    onSuccess: () => (editingWorkspace.value = false),
  })
}

const destroyWorkspace = () => {
  if (!confirm('Are you sure you want to delete this workspace?')) return
  deletingWorkspace.value = true
  router.delete(`/workspaces/${props.workspace.id}`, {
    onFinish: () => (deletingWorkspace.value = false),
  })
}

// Create task
const taskForm = useForm({
  title: '',
  description: '',
  deadline: '' as string | null,
})

const createTask = () => {
  if (!taskForm.title.trim()) return

  taskForm.post(`/workspaces/${props.workspace.id}/tasks`, {
    preserveScroll: true,
    onSuccess: () => {
      taskForm.reset()
    }
  })
}

// Delete task
const deletingTaskId = ref<number | null>(null)
const deleteTask = (task: Task) => {
  if (!confirm('Delete this task?')) return
  deletingTaskId.value = task.id
  router.delete(`/workspaces/${props.workspace.id}/tasks/${task.id}`, {
    preserveScroll: true,
    onFinish: () => (deletingTaskId.value = null),
  })
}

// Toggle complete
const togglingId = ref<number | null>(null)
const toggleComplete = (task: Task) => {
  togglingId.value = task.id
  router.patch(`/workspaces/${props.workspace.id}/tasks/${task.id}/toggle-complete`, {}, {
    preserveScroll: true,
    onFinish: () => (togglingId.value = null),
  })
}

// Edit task
const editingTaskId = ref<number | null>(null)
const editForm = useForm({
  title: '',
  description: '' as string | null,
  deadline: '' as string | null,
  is_completed: false,
})

const startEdit = (task: Task) => {
  editingTaskId.value = task.id
  editForm.defaults({
    title: task.title,
    description: task.description ?? '',
    deadline: task.deadline_editable ?? '',
    is_completed: task.is_completed,
  })
  editForm.reset()
}

const cancelEdit = () => {
  editingTaskId.value = null
}

const updateTask = (task: Task) => {
  editForm.put(`/workspaces/${props.workspace.id}/tasks/${task.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      editingTaskId.value = null
    }
  })
}
</script>

<template>
  <Head :title="`Workspace: ${props.workspace.name}`" />

  <AppLayout>
    <div class="p-4 space-y-8 max-w-3xl">
      <FlashToast />

      <!-- Workspace Header -->
      <section class="space-y-2">
        <h1 class="text-2xl font-bold">Workspace Details</h1>

        <label class="block font-medium">Name</label>
        <input v-model="name" :readonly="!editingWorkspace" class="border px-3 py-2 rounded w-full dark:border-gray-700" />

        <div class="flex gap-2 mt-2">
          <button v-if="!editingWorkspace" @click="editingWorkspace = true" class="bg-blue-600 text-white px-4 py-2 rounded">Edit</button>
          <button v-else @click="updateWorkspace" class="bg-green-600 text-white px-4 py-2 rounded">Save</button>
          <button v-if="editingWorkspace" @click="editingWorkspace = false" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
        </div>

        <div class="mt-4">
          <button @click="destroyWorkspace" :disabled="deletingWorkspace" class="bg-red-600 text-white px-4 py-2 rounded">
            {{ deletingWorkspace ? 'Deleting...' : 'Delete Workspace' }}
          </button>
        </div>
      </section>

      <hr />

      <!-- Tasks Section -->
      <section class="space-y-4">
        <h2 class="text-xl font-semibold">Tasks</h2>

        <!-- Create Task -->
        <form @submit.prevent="createTask" class="space-y-2 border p-4 rounded bg-white dark:bg-sidebar">
          <div>
            <label class="block text-sm font-medium">Title</label>
            <input v-model="taskForm.title" type="text" class="border px-3 py-2 rounded w-full dark:border-gray-700" placeholder="Task title" />
          </div>

          <div>
            <label class="block text-sm font-medium">Description (optional)</label>
            <textarea v-model="taskForm.description" class="border px-3 py-2 rounded w-full dark:border-gray-700" rows="2" placeholder="Short description" />
          </div>

          <div>
            <label class="block text-sm font-medium">Due date (optional)</label>
            <input v-model="taskForm.deadline" type="datetime-local" class="border px-3 py-2 rounded w-full dark:border-gray-700" />
          </div>

          <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded" :disabled="taskForm.processing">
            {{ taskForm.processing ? 'Creating...' : 'Add Task' }}
          </button>
        </form>

        <!-- Task List -->
        <ul class="space-y-2">
          <li v-for="task in props.tasks" :key="task.id" class="border p-4 rounded bg-white dark:bg-sidebar">
            <!-- View Mode -->
            <template v-if="editingTaskId !== task.id">
              <div class="flex items-start justify-between gap-4">
                <div class="flex items-start gap-2">
                  <input type="checkbox" :checked="task.is_completed" :disabled="togglingId === task.id" @change="toggleComplete(task)" class="mt-1" />
                  <div>
                    <p :class="task.is_completed ? 'line-through text-gray-500' : ''" class="font-medium">{{ task.title }}</p>
                    <p v-if="task.description" class="text-sm text-gray-600 dark:text-gray-300">{{ task.description }}</p>
                    <p v-if="task.is_completed" class="text-xs text-green-600">
                    Status: Completed
                    </p>
                    <p v-else class="text-xs text-yellow-600">
                    Status: Incomplete
                    </p>
                    <p
                    v-if="task.is_completed"
                    class="text-xs text-green-700 dark:text-green-400"
                    >
                    Completed {{ task.completed_at_human }}
                    </p>
                    <p v-if="task.deadline && !task.is_completed" class="text-xs text-gray-500">Due: {{ task.deadline_remaining  }}</p>
                  </div>
                </div>
                <div class="flex gap-2">
                    <button class="text-blue-600 hover:underline" @click="startEdit(task)">
                        Edit
                    </button>

                    <button
                        class="text-red-600 hover:underline"
                        :disabled="deletingTaskId === task.id"
                        @click="deleteTask(task)"
                    >
                        {{ deletingTaskId === task.id ? 'Deleting...' : 'Delete' }}
                    </button>

                    <button
                        v-if="!task.is_completed"
                        class="text-green-600 hover:underline"
                        :disabled="togglingId === task.id"
                        @click="toggleComplete(task)"
                    >
                        {{ togglingId === task.id ? 'Completing...' : 'Complete' }}
                    </button>
                </div>
              </div>
            </template>

            <!-- Edit Mode -->
            <template v-else>
              <div class="space-y-2">
                <div>
                  <label class="block text-sm font-medium">Title</label>
                  <input v-model="editForm.title" type="text" class="border px-3 py-2 rounded w-full dark:border-gray-700" />
                </div>

                <div>
                  <label class="block text-sm font-medium">Description</label>
                  <textarea v-model="editForm.description" rows="2" class="border px-3 py-2 rounded w-full dark:border-gray-700" />
                </div>

                <div>
                  <label class="block text-sm font-medium">Due at</label>
                  <input v-model="editForm.deadline" type="datetime-local" class="border px-3 py-2 rounded w-full dark:border-gray-700" />
                </div>

                <div class="flex items-center gap-2">
                  <input id="is_completed" type="checkbox" v-model="editForm.is_completed" />
                  <label for="completed">Completed</label>
                </div>

                <div class="flex gap-2">
                  <button @click="updateTask(task)" class="bg-green-600 text-white px-4 py-2 rounded" :disabled="editForm.processing">
                    {{ editForm.processing ? 'Saving...' : 'Save' }}
                  </button>
                  <button @click="cancelEdit" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
                </div>
              </div>
            </template>
          </li>
        </ul>
      </section>
    </div>
  </AppLayout>
</template>
