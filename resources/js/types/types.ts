// resources/js/types/index.ts

export type User = {
  id: number
  name: string
  email: string
}

export type Workspace = {
  id: number
  name: string
}

export type Task = {
  id: number
  workspace_id: number
  title: string
  description?: string | null
  is_completed: boolean
  deadline?: string | null
  created_at?: string
  updated_at?: string
}
