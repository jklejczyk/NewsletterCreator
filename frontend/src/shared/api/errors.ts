import axios, {type AxiosError} from 'axios'

export type ValidationErrors = Record<string, string[]>

export class ValidationError extends Error {
    readonly status = 422

    constructor(
        message: string,
        public readonly errors: ValidationErrors,
    ) {
        super(message)
        this.name = 'ValidationError'
    }
}

export class ApiError extends Error {
    constructor(
        message: string,
        public readonly status: number,
    ) {
        super(message)
        this.name = 'ApiError'
    }
}

export function fromAxiosError(error: AxiosError): Error {
    const status = error.response?.status
    const data = error.response?.data as {
        message?: string;
        errors?: ValidationErrors
    } | undefined

    if (status === 422 && data?.errors) {
        return new ValidationError(data.message ?? 'Validation failed', data.errors)
    }

    if (status) {
        return new ApiError(data?.message ?? error.message, status)
    }

    return new ApiError(error.message || 'Network error', 0)
}

export function isAxiosError(error: unknown): error is
    AxiosError {
    return axios.isAxiosError(error)
}