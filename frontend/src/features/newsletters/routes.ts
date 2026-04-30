import type {RouteRecordRaw} from 'vue-router'

export const newsletterRoutes: RouteRecordRaw[] = [
    {
        path: 'newsletters',
        name: 'newsletter-list',
        component: () => import('./views/NewsletterListView.vue'),
    },
    {
        path: 'newsletters/:id',
        name: 'newsletter-detail',
        component: () => import('./views/NewsletterDetailView.vue'),
    },
]