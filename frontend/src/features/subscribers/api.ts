import { client } from '@/shared/api/client'
import type { MessageResponse } from '@/shared/types/api'
import type { SubscribePayload } from './types'

export async function subscribe(payload: SubscribePayload): Promise<MessageResponse> {
    const response = await client.post<MessageResponse>('/subscribers', payload)
    return response.data
}

export async function confirmSubscription(token: string): Promise<MessageResponse> {
    const response = await client.get<MessageResponse>(`/subscribers/confirm/${token}`)
    return response.data
}

export async function unsubscribe(id: number): Promise<MessageResponse> {
    const response = await client.delete<MessageResponse>(`/subscribers/${id}`)
    return response.data
}