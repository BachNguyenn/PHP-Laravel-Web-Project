<template>
    <div class="wrapper">
        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <router-link to="/" class="brand-link">
                <i class="fas fa-dumbbell brand-image ms-3" style="font-size: 1.5rem;"></i>
                <span class="brand-text fw-light">GYM Manager</span>
            </router-link>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <i class="fas fa-user-circle text-light" style="font-size: 2.1rem;"></i>
                    </div>
                    <div class="info">
                        <a href="#" class="d-block">{{ userName }}</a>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <!-- Dashboard -->
                        <li class="nav-item">
                            <router-link to="/dashboard" class="nav-link" :class="{ active: isActive('/dashboard') }">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </router-link>
                        </li>

                        <!-- Members -->
                        <li class="nav-item">
                            <router-link to="/members" class="nav-link" :class="{ active: isActive('/members') }">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Members</p>
                            </router-link>
                        </li>

                        <!-- Finance Header -->
                        <li class="nav-header">FINANCE MANAGEMENT</li>

                        <!-- Expenses -->
                        <li class="nav-item">
                            <router-link to="/expenses" class="nav-link" :class="{ active: isActive('/expenses') }">
                                <i class="nav-icon fas fa-money-bill-wave"></i>
                                <p>Expenses</p>
                            </router-link>
                        </li>

                        <!-- Invoices -->
                        <li class="nav-item">
                            <router-link to="/invoices" class="nav-link" :class="{ active: isActive('/invoices') }">
                                <i class="nav-icon fas fa-file-invoice-dollar"></i>
                                <p>Invoices</p>
                            </router-link>
                        </li>

                        <!-- System Header -->
                        <li class="nav-header">SYSTEM SETTINGS</li>

                        <!-- Settings -->
                        <li class="nav-item">
                            <router-link to="/settings" class="nav-link" :class="{ active: isActive('/settings') }">
                                <i class="nav-icon fas fa-cog"></i>
                                <p>Settings</p>
                            </router-link>
                        </li>

                        <li class="nav-item mt-4">
                            <a href="#" class="nav-link text-warning" @click.prevent="logout">
                                <i class="nav-icon fas fa-sign-out-alt"></i>
                                <p>Logout</p>
                            </a>
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
            <strong>&copy; 2026 <a href="#">GYM Manager</a>.</strong>
            All rights reserved.
            <div class="float-end d-none d-sm-inline-block">
                <b>Version</b> 1.0.0
            </div>
        </footer>
    </div>
</template>

<script>
import { ref, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useToast } from 'vue-toastification';
import api from '@/utils/axios';

export default {
    name: 'AdminLayout',
    setup() {
        const router = useRouter();
        const route = useRoute();
        const toast = useToast();

        const userName = computed(() => {
            try {
                const user = localStorage.getItem('user');
                if (user && user !== 'undefined') {
                    return JSON.parse(user).name || 'Admin';
                }
                return 'Admin';
            } catch (e) {
                return 'Admin';
            }
        });

        const isActive = (path) => {
            return route.path === path || route.path.startsWith(path + '/');
        };

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

        return {
            userName,
            isActive,
            logout,
        };
    },
};
</script>

<style scoped>
.wrapper {
    min-height: 100vh;
}

/* Sidebar fixed */
.main-sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    overflow-y: auto;
    z-index: 1038;
    width: 250px;
}

/* Content wrapper margin for sidebar */
.content-wrapper {
    margin-left: 250px;
    min-height: 100vh;
    padding-top: 0;
}

/* Footer margin for sidebar */
.main-footer {
    margin-left: 250px;
}

/* Brand link styling */
.brand-link {
    display: flex;
    align-items: center;
    padding: 0.8125rem 0.5rem;
    font-size: 1.25rem;
    line-height: 1.5;
    text-decoration: none;
    background-color: rgba(0, 0, 0, 0.15);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 0.8);
}

.brand-link:hover {
    color: #fff;
}

.brand-text {
    margin-left: 0.5rem;
}

/* User panel */
.user-panel .info a {
    color: #fff;
    text-decoration: none;
}

/* Nav link styling */
.nav-sidebar .nav-link {
    color: rgba(255, 255, 255, 0.8);
    border-radius: 0;
    margin-bottom: 0;
}

.nav-sidebar .nav-link:hover {
    background-color: rgba(255, 255, 255, 0.1);
    color: #fff;
}

.nav-sidebar .nav-link.active {
    background-color: #007bff !important;
    color: #fff !important;
}

.nav-sidebar .nav-icon {
    margin-right: 0.5rem;
    width: 1.6rem;
    text-align: center;
}

/* Nav header */
.nav-header {
    color: rgba(255, 255, 255, 0.5);
    font-size: 0.75rem;
    padding: 0.75rem 1rem 0.5rem;
    text-transform: uppercase;
}

/* Responsive */
@media (max-width: 991.98px) {
    .main-sidebar {
        transform: translateX(-250px);
        transition: transform 0.3s ease-in-out;
    }
    
    .main-sidebar.show {
        transform: translateX(0);
    }
    
    .content-wrapper,
    .main-footer {
        margin-left: 0;
    }
}
</style>
