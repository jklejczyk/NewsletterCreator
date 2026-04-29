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

const ERROR_MESSAGES: Record<number, string> = {
    0: 'Brak połączenia z serwerem. Sprawdź internet i spróbuj ponownie.',
    401: 'Wymagane uwierzytelnienie.',
    403: 'Brak uprawnień do wykonania tej akcji.',
    404: 'Nie znaleziono zasobu.',
    422: 'Sprawdź wprowadzone dane.',
    500: 'Wystąpił problem po stronie serwera. Spróbuj za chwilę.',
    502: 'Serwer chwilowo niedostępny.',
    503: 'Serwer chwilowo niedostępny.',
}

function messageForStatus(status: number): string {
    return ERROR_MESSAGES[status] ?? `Błąd HTTP ${status}.`
}

export function fromAxiosError(error: AxiosError): Error {
    const status = error.response?.status
    const data = error.response?.data as { message?: string; errors?: ValidationErrors } | undefined

    if (status === 422 && data?.errors) {
        return new ValidationError(messageForStatus(422), data.errors)
    }

    if (status) {
        return new ApiError(messageForStatus(status), status)
    }

    return new ApiError(messageForStatus(0), 0)
}

export function isAxiosError(error: unknown): error is
    AxiosError {
    return axios.isAxiosError(error)
}