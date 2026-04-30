import {computed} from 'vue'
import {useRoute, useRouter, type LocationQuery} from 'vue-router'
import type {NewsletterStatus, NewsletterFilters} from '../types'

const VALID_STATUSES: NewsletterStatus[] = ['draft', 'sending', 'sent', 'failed']

function parseFilters(query: LocationQuery): NewsletterFilters {
    const s = query.status
    const status =
        typeof s === 'string' && VALID_STATUSES.includes(s as NewsletterStatus)
            ? (s as NewsletterStatus)
            : undefined

    const dateFrom = typeof query.date_from === 'string' ? query.date_from : undefined
    const dateTo = typeof query.date_to === 'string' ? query.date_to : undefined

    const pageRaw = typeof query.page === 'string' ? Number(query.page) : NaN
    const page = Number.isFinite(pageRaw) && pageRaw > 0 ? pageRaw : undefined

    return {status, date_from: dateFrom, date_to: dateTo, page}
}

function buildQuery(filters: NewsletterFilters): Record<string,
    string> {
    const query: Record<string, string> = {}
    if (filters.status) query.status = filters.status
    if (filters.date_from) query.date_from = filters.date_from
    if (filters.date_to) query.date_to = filters.date_to
    if (filters.page) query.page = String(filters.page)
    return query
}

export function useNewsletterFilters() {
    const route = useRoute()
    const router = useRouter()

    const filters = computed<NewsletterFilters>(() => parseFilters(route.query))

    const hasActiveFilters = computed(() => {
        const f = filters.value
        return !!(f.status || f.date_from || f.date_to)
    })

    function patchFilters(
        patch: Partial<NewsletterFilters>,
        options: { resetPage?: boolean } = {},
    ) {
        const merged: NewsletterFilters = {
            ...filters.value,
            ...patch,
            ...(options.resetPage && {page: undefined}),
        }
        return router.replace({query: buildQuery(merged)})
    }

    function resetFilters() {
        return router.replace({query: {}})
    }

    return {
        filters,
        hasActiveFilters,
        patchFilters,
        resetFilters
    }
}