export interface Paginated<T> {
    data: T[]
    links: PaginationLinks
    meta: PaginationMeta
}

export interface PaginationLinks {
    first: string | null
    last: string | null
    prev: string | null
    next: string | null
}

export interface PaginationMeta {
    current_page: number
    last_page: number
    per_page: number
    total: number
    from: number | null
    to: number | null
    path: string
    links: PageLink[]
}

export interface PageLink {
    url: string | null
    label: string
    active: boolean
}

export interface Resource<T> {
    data: T
}

export interface MessageResponse {
    message: string
}