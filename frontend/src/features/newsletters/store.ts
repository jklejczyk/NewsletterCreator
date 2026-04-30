import { computed, ref } from 'vue'
import { defineStore } from 'pinia'
import type {PaginationMeta} from "@/shared/types/api";
import type {RequestStatus} from "@/shared/types/status";
import type {Newsletter, NewsletterFilters} from "@/features/newsletters/types";
import {fetchNewsletters, fetchNewsletterStats, sendNewsletter} from "@/features/newsletters/api";

export const useNewsletterStore = defineStore('newsletters', () => {
    const newsletters = ref<Newsletter[]>([])
    const meta = ref<PaginationMeta | null>(null)
    const status = ref<RequestStatus>('idle')
    const currentNewsletter = ref<Newsletter | null>(null)
    const detailStatus = ref<RequestStatus>('idle')
    const detailError = ref<string | null>(null)
    const sendStatus = ref<RequestStatus>('idle')
    const sendError = ref<string | null>(null)
    const error = ref<string | null>(null)

    const isLoading = computed(() => status.value === 'loading')
    const isEmpty = computed(() => status.value === 'success' && newsletters.value.length === 0)
    const hasError = computed(() => status.value === 'error')
    const isDetailLoading = computed(() => detailStatus.value === 'loading')
    const isSending = computed(() => sendStatus.value === 'loading')

    async function fetchList(filters: NewsletterFilters) {
        status.value = 'loading'
        error.value = null
        try {
            const response = await fetchNewsletters(filters)
            newsletters.value = response.data
            meta.value = response.meta
            status.value = 'success'
        } catch (err) {
            status.value = 'error'
            error.value = err instanceof Error ? err.message : 'Unknown error'
        }
    }

    async function fetchStats(id: number) {
        detailStatus.value = 'loading'
        detailError.value = null
        try {
            currentNewsletter.value = await fetchNewsletterStats(id)
            detailStatus.value = 'success'
        } catch (err) {
            detailStatus.value = 'error'
            detailError.value = err instanceof Error ? err.message : 'Unknown error'
        }
    }

    async function triggerSend(): Promise<string | null> {
        sendStatus.value = 'loading'
        sendError.value = null
        try {
            const response = await sendNewsletter()
            sendStatus.value = 'success'
            return response.message
        } catch (err) {
            sendStatus.value = 'error'
            sendError.value = err instanceof Error ? err.message : 'Unknown error'
            return null
        }
    }

    return {
        newsletters,
        meta,
        status,
        error,
        isLoading,
        isEmpty,
        hasError,
        fetchList,
        currentNewsletter,
        detailStatus,
        detailError,
        isDetailLoading,
        fetchStats,
        sendStatus,
        sendError,
        isSending,
        triggerSend,
    }
})
