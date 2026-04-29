export type ArticleCategory = 'technology' | 'business' | 'science' | 'general'
export type ArticleSource = 'newsapi' | 'rss'

export interface Article {
    id: number
    title: string
    summary: string | null
    url: string
    category: ArticleCategory | null
    source: ArticleSource
    published_at: string | null
}

export interface ArticleFilters {
    category?: ArticleCategory
    date_from?: string
    date_to?: string
    per_page?: number
    page?: number
}
