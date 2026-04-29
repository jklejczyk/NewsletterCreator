import { computed, ref } from 'vue'
import { defineStore } from 'pinia'
import { fetchArticles } from './api'
import type {Article, ArticleCategory, ArticleFilters} from './types'
import type { PaginationMeta } from '@/shared/types/api'

type RequestStatus = 'idle' | 'loading' | 'success' | 'error'

export const useArticleStore = defineStore('articles', () => {
    const articles = ref<Article[]>([])
    const meta = ref<PaginationMeta | null>(null)
    const status = ref<RequestStatus>('idle')
    const error = ref<string | null>(null)

    const isLoading = computed(() => status.value === 'loading')
    const isEmpty = computed(() => status.value === 'success' && articles.value.length === 0)
    const hasError = computed(() => status.value === 'error')

    async function fetchList(filters: ArticleFilters) {
        status.value = 'loading'
        error.value = null
        try {
            const response = await fetchArticles(filters)
            articles.value = response.data
            meta.value = response.meta
            status.value = 'success'
        } catch (e) {
            status.value = 'error'
            error.value = e instanceof Error ? e.message : 'Unknown error'
        }
    }

    return {
        articles,
        meta,
        status,
        error,
        isLoading,
        isEmpty,
        hasError,
        fetchList,
    }
})