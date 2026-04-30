import type {NewsletterStatus} from './types'

interface StatusMeta {
    label: string
    colorClasses: string
}

export const STATUS_META: Record<NewsletterStatus, StatusMeta> = {
    draft: {
        label: 'Szkic', colorClasses: 'bg-slate-100 text-slate - 800'
    },
    sending: {
        label: 'Wysyłanie', colorClasses: 'bg-blue-100 text-blue - 800'
    },
    sent: {
        label: 'Wysłany', colorClasses: 'bg-emerald-100 text-emerald - 800'
    },
    failed: {
        label: 'Błąd', colorClasses: 'bg-red-100 text-red - 800'
    },
}

export const STATUS_VALUES = Object.keys(STATUS_META) as NewsletterStatus[]