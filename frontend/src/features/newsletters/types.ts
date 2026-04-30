import type { Subscriber } from '@/features/subscribers/types'

export type NewsletterStatus = 'draft' | 'sending' | 'sent' | 'failed'

export interface Newsletter {
    id: number
    subject: string
    status: NewsletterStatus
    sent_at: string | null
    recipient_count: number
    sends?: NewsletterSend[]
}

export interface NewsletterSend {
    subscriber: Subscriber
    sent_at: string
}

export interface NewsletterFilters {
    status?: NewsletterStatus
    date_from?: string
    date_to?: string
    per_page?: number,
    page?: number
}