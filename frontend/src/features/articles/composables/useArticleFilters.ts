import {computed} from 'vue'
import {useRoute, useRouter, type LocationQuery} from 'vue-router'
import type {ArticleCategory, ArticleFilters} from '../types'

const VALID_CATEGORIES: ArticleCategory[] = ['technology', 'business', 'science', 'general']

function parseFilters(query: LocationQuery): ArticleFilters {
    const cat = query.category
    const category = typeof cat === 'string' && VALID_CATEGORIES.includes(cat as ArticleCategory)
        ? (cat as ArticleCategory) : undefined

    const dateFrom = typeof query.date_from === 'string' ? query.date_from : undefined
    const dateTo = typeof query.date_to === 'string' ? query.date_to : undefined

    const pageRaw = typeof query.page === 'string' ? Number(query.page) : NaN
    const page = Number.isFinite(pageRaw) && pageRaw > 0 ? pageRaw : undefined

    return {
        category, date_from: dateFrom, date_to: dateTo, page
    }
}

function buildQuery(filters: ArticleFilters): Record<string, string> {
    const query: Record<string, string> = {}

    if (filters.category) query.category = filters.category
    if (filters.date_from) query.date_from = filters.date_from
    if (filters.date_to) query.date_to = filters.date_to
    if (filters.page) query.page = String(filters.page)

    return query
}

export function useArticleFilters() {
    const route = useRoute()
    const router = useRouter()

    const filters = computed<ArticleFilters>(() => parseFilters(route.query))

    const hasActiveFilters = computed(() => {
        const f = filters.value
        return !!(f.category || f.date_from || f.date_to)
    })

    function patchFilters(patch: Partial<ArticleFilters>, options: { resetPage?: boolean } = {}) {
        const merged: ArticleFilters = {
            ...filters.value,
            ...patch,
            ...(options.resetPage && { page: undefined }),
        }
        return router.replace({ query: buildQuery(merged) })
    }

    function resetFilters() {
        return router.replace({ query: {} })
    }

    return {
        filters,
        hasActiveFilters,
        patchFilters,
        resetFilters,
    }
}