import {client} from '@/shared/api/client'
import type {Paginated, Resource} from '@/shared/types/api'
import type {Article, ArticleFilters} from './types'

export async function fetchArticles(filters: ArticleFilters = {}): Promise<Paginated<Article>> {
    const response = await client.get<Paginated<Article>>('/articles', {
        params: filters,
    })
    return response.data
}

export async function fetchArticle(id: number): Promise<Article> {
    const response = await client.get<Resource<Article>>(`/articles/${id}`)
    return response.data.data
}