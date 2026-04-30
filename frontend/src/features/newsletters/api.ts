import { client } from '@/shared/api/client'
import type {MessageResponse, Paginated, Resource} from '@/shared/types/api'
import type {Newsletter, NewsletterFilters} from "@/features/newsletters/types";

export async function fetchNewsletters(filters: NewsletterFilters = {}): Promise<Paginated<Newsletter>> {
    const response = await client.get<Paginated<Newsletter>>('/newsletters', {
        params: filters,
    })
    return response.data
}

export async function fetchNewsletterStats(id: number): Promise<Newsletter> {
    const response = await client.get<Resource<Newsletter>>(`/newsletters/${id}/stats`)
    return response.data.data
}

export async function sendNewsletter(): Promise<MessageResponse> {
    const response = await client.post<MessageResponse>('/newsletters/send')
    return response.data
}
