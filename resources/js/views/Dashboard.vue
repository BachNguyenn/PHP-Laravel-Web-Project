<template>
    <div>
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">
                            <i class="fas fa-tachometer-alt me-2 text-primary"></i>
                            Dashboard
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item active">
                                <i class="fas fa-home me-1"></i> Dashboard
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Small boxes (Stat box) -->
                <div class="row">
                    <!-- Total Members -->
                    <div class="col-lg-4 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ stats.totalMembers }}</h3>
                                <p>Total Members</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <router-link to="/members" class="small-box-footer">
                                View all <i class="fas fa-arrow-circle-right"></i>
                            </router-link>
                        </div>
                    </div>

                    <!-- Active Members -->
                    <div class="col-lg-4 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ stats.activeMembers }}</h3>
                                <p>Active Members</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <router-link to="/members" class="small-box-footer">
                                More info <i class="fas fa-arrow-circle-right"></i>
                            </router-link>
                        </div>
                    </div>

                    <!-- Inactive Members -->
                    <div class="col-lg-4 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>{{ stats.inactiveMembers }}</h3>
                                <p>Inactive Members</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-user-times"></i>
                            </div>
                            <router-link to="/members" class="small-box-footer">
                                More info <i class="fas fa-arrow-circle-right"></i>
                            </router-link>
                        </div>
                    </div>
                </div>

                <!-- Second Row -->
                <div class="row">
                    <!-- Total Invoices -->
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ stats.totalInvoices }}</h3>
                                <p>Total Invoices</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                            <router-link to="/invoices" class="small-box-footer">
                                View all <i class="fas fa-arrow-circle-right"></i>
                            </router-link>
                        </div>
                    </div>

                    <!-- Monthly Revenue -->
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3>{{ formatCurrency(stats.monthlyRevenue) }}</h3>
                                <p>This Month Revenue</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <router-link to="/invoices" class="small-box-footer">
                                More info <i class="fas fa-arrow-circle-right"></i>
                            </router-link>
                        </div>
                    </div>

                    <!-- Total Expenses -->
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-secondary">
                            <div class="inner">
                                <h3>{{ formatCurrency(stats.totalExpenses) }}</h3>
                                <p>Total Expenses</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <router-link to="/expenses" class="small-box-footer">
                                View all <i class="fas fa-arrow-circle-right"></i>
                            </router-link>
                        </div>
                    </div>

                    <!-- Net Profit -->
                    <div class="col-lg-3 col-6">
                        <div class="small-box" :class="stats.netProfit >= 0 ? 'bg-teal' : 'bg-maroon'">
                            <div class="inner">
                                <h3>{{ formatCurrency(stats.netProfit) }}</h3>
                                <p>Net Profit</p>
                            </div>
                            <div class="icon">
                                <i :class="stats.netProfit >= 0 ? 'fas fa-chart-line' : 'fas fa-chart-line-down'"></i>
                            </div>
                            <a href="#" class="small-box-footer">
                                <i :class="stats.netProfit >= 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'"></i>
                                {{ stats.netProfit >= 0 ? 'Profit' : 'Loss' }}
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions & Welcome Card -->
                <div class="row">
                    <!-- Quick Actions -->
                    <div class="col-lg-6">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-bolt me-2"></i>
                                    Quick Actions
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <router-link to="/members" class="btn btn-primary btn-lg w-100">
                                            <i class="fas fa-user-plus me-2"></i>
                                            Add Member
                                        </router-link>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <router-link to="/invoices" class="btn btn-success btn-lg w-100">
                                            <i class="fas fa-file-invoice me-2"></i>
                                            New Invoice
                                        </router-link>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <router-link to="/expenses" class="btn btn-warning btn-lg w-100">
                                            <i class="fas fa-receipt me-2"></i>
                                            Add Expense
                                        </router-link>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <router-link to="/settings" class="btn btn-secondary btn-lg w-100">
                                            <i class="fas fa-cog me-2"></i>
                                            Settings
                                        </router-link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Welcome Card -->
                    <div class="col-lg-6">
                        <div class="card card-success card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-dumbbell me-2"></i>
                                    Welcome to GYM Manager
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-circle bg-primary me-3">
                                        <i class="fas fa-user text-white"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0">Hello, {{ userName }}!</h5>
                                        <small class="text-muted">{{ currentDate }}</small>
                                    </div>
                                </div>
                                <p class="text-muted">
                                    Manage your gym members, track finances, and grow your business efficiently with this admin panel.
                                </p>
                                <div class="progress mb-2" style="height: 20px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         :style="{ width: membershipProgress + '%' }"
                                         :aria-valuenow="membershipProgress" aria-valuemin="0" aria-valuemax="100">
                                        {{ membershipProgress }}% Active Members
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import api from '@/utils/axios';

export default {
    name: 'Dashboard',
    setup() {
        const stats = ref({
            totalMembers: 0,
            activeMembers: 0,
            inactiveMembers: 0,
            totalInvoices: 0,
            monthlyRevenue: 0,
            totalExpenses: 0,
            netProfit: 0,
        });

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

        const currentDate = computed(() => {
            return new Date().toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
            });
        });

        const membershipProgress = computed(() => {
            if (stats.value.totalMembers === 0) return 0;
            return Math.round((stats.value.activeMembers / stats.value.totalMembers) * 100);
        });

        const formatCurrency = (value) => {
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND',
                maximumFractionDigits: 0,
            }).format(value || 0);
        };

        const fetchStats = async () => {
            try {
                // Fetch members
                const membersResponse = await api.get('/members?per_page=1000');
                const members = membersResponse.data.data.data || [];
                
                stats.value.totalMembers = members.length;
                stats.value.activeMembers = members.filter(m => m.status).length;
                stats.value.inactiveMembers = members.filter(m => !m.status).length;

                // Fetch invoices
                try {
                    const invoicesResponse = await api.get('/invoices?per_page=1000');
                    const invoices = invoicesResponse.data.data.data || [];
                    stats.value.totalInvoices = invoices.length;
                    
                    // Calculate monthly revenue
                    const currentMonth = new Date().getMonth();
                    const currentYear = new Date().getFullYear();
                    stats.value.monthlyRevenue = invoices
                        .filter(inv => {
                            const invDate = new Date(inv.created_at);
                            return invDate.getMonth() === currentMonth && invDate.getFullYear() === currentYear;
                        })
                        .reduce((sum, inv) => sum + (inv.amount || 0), 0);
                } catch (e) {
                    console.log('Invoices API not available');
                }

                // Fetch expenses
                try {
                    const expensesResponse = await api.get('/expenses?per_page=1000');
                    const expenses = expensesResponse.data.data.data || [];
                    stats.value.totalExpenses = expenses.reduce((sum, exp) => sum + (exp.amount || 0), 0);
                } catch (e) {
                    console.log('Expenses API not available');
                }

                // Calculate net profit
                stats.value.netProfit = stats.value.monthlyRevenue - stats.value.totalExpenses;
            } catch (error) {
                console.error('Failed to fetch stats:', error);
            }
        };

        onMounted(() => {
            fetchStats();
        });

        return {
            stats,
            userName,
            currentDate,
            membershipProgress,
            formatCurrency,
        };
    },
};
</script>

<style scoped>
.small-box {
    border-radius: 0.5rem;
    margin-bottom: 20px;
    transition: all 0.3s ease;
}

.small-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.small-box .inner h3 {
    font-size: 2rem;
    font-weight: 700;
    margin: 0 0 10px 0;
    padding: 0;
    white-space: nowrap;
}

.small-box .icon {
    transition: all 0.3s ease;
}

.small-box:hover .icon {
    font-size: 80px;
}

.icon-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.bg-teal {
    background-color: #20c997 !important;
}

.bg-maroon {
    background-color: #d81b60 !important;
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
}

.btn-lg {
    padding: 1rem;
    font-size: 0.9rem;
}
</style>
