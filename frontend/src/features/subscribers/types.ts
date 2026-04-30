import type { ArticleCategory } from '@/features/articles/types'

export interface Subscriber {
    id: number
    email: string
    name: string
}

export interface SubscribePayload {
    email: string
    name: string
    preferences: ArticleCategory[]
}