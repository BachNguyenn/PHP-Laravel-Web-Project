// Vue Router configuration
import Login from '@/views/Login.vue';
import Dashboard from '@/views/Dashboard.vue';
import Members from '@/views/Members.vue';
import Expenses from '@/views/Expenses.vue';
import Invoices from '@/views/Invoices.vue';
import InvoiceDetail from '@/views/InvoiceDetail.vue';
import Settings from '@/views/Settings.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';

const routes = [
    {
        path: '/login',
        name: 'Login',
        component: Login,
        meta: { requiresAuth: false },
    },
    {
        path: '/',
        component: AdminLayout,
        meta: { requiresAuth: true },
        children: [
            {
                path: '',
                redirect: '/dashboard',
            },
            {
                path: 'dashboard',
                name: 'Dashboard',
                component: Dashboard,
            },
            {
                path: 'members',
                name: 'Members',
                component: Members,
            },
            {
                path: 'expenses',
                name: 'Expenses',
                component: Expenses,
            },
            {
                path: 'invoices',
                name: 'Invoices',
                component: Invoices,
            },
            {
                path: 'invoices/:id',
                name: 'InvoiceDetail',
                component: InvoiceDetail,
            },
            {
                path: 'settings',
                name: 'Settings',
                component: Settings,
            },
        ],
    },
    {
        path: '/:pathMatch(.*)*',
        redirect: '/dashboard',
    },
];

export default routes;
