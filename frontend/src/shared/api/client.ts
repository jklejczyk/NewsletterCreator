import axios, {type AxiosError} from 'axios'
import {fromAxiosError} from './errors'

export const client = axios.create({
    baseURL: import.meta.env.VITE_API_URL,
    headers: {
        Accept: 'application/json',
    },
})

client.interceptors.request.use((config) => {
    const token = import.meta.env.VITE_ADMIN_TOKEN
    if (token) {
        config.headers.Authorization = `Bearer ${token}`
    }
    return config
})

client.interceptors.response.use((response) => response,
    (error: AxiosError) =>
        Promise.reject(fromAxiosError(error)),
)