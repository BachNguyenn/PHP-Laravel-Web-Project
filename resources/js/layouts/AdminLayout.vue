<template>
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-dark navbar-primary">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <router-link to="/dashboard" class="nav-link">Home</router-link>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-2"></i>{{ userName }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#" @click.prevent="logout">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a></li>
                    </ul>
                </li>
            </ul>
        </nav>

        <!-- Sidebar -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="/" class="brand-link">
                <span class="brand-text fw-bold ms-2">
                    <i class="fas fa-dumbbell me-2"></i>GYM Manager
                </span>
            </a>
            <div class="sidebar">
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" role="menu">
                        <li class="nav-item">
                            <router-link to="/dashboard" class="nav-link" active-class="active">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <router-link to="/members" class="nav-link" active-class="active">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Members</p>
                            </router-link>
                        </li>
                        <li class="nav-header">FINANCE</li>
                        <li class="nav-item">
                            <router-link to="/expenses" class="nav-link" active-class="active">
                                <i class="nav-icon fas fa-money-bill-wave"></i>
                                <p>Expenses</p>
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <router-link to="/invoices" class="nav-link" active-class="active">
                                <i class="nav-icon fas fa-file-invoice-dollar"></i>
                                <p>Invoices</p>
                            </router-link>
                        </li>
                        <li class="nav-header">SYSTEM</li>
                        <li class="nav-item">
                            <router-link to="/settings" class="nav-link" active-class="active">
                                <i class="nav-icon fas fa-cog"></i>
                                <p>Settings</p>
                            </router-link>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <router-view />
        </div>

        <!-- Footer -->
        <footer class="main-footer">
            <strong>&copy; 2026 GYM Manager.</strong> All rights reserved.
        </footer>
    </div>
</template>

<script>
import { useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';
import api from '@/utils/axios';

export default {
    name: 'AdminLayout',
    setup() {
        const router = useRouter();
        const toast = useToast();

        const userName = localStorage.getItem('user') 
            ? JSON.parse(localStorage.getItem('user')).name 
            : 'User';

        const logout = async () => {
            try {
                await api.post('/logout');
                localStorage.removeItem('access_token');
                localStorage.removeItem('user');
                toast.success('Logged out successfully');
                router.push('/login');
            } catch (error) {
                localStorage.removeItem('access_token');
                localStorage.removeItem('user');
                router.push('/login');
            }
        };

        return { userName, logout };
    },
};
</script>

<style scoped>
.wrapper {
    min-height: 100vh;
}
.content-wrapper {
    min-height: calc(100vh - 60px);
}
</style>
