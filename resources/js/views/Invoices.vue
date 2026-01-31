<template>
    <div>
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">
                            <i class="fas fa-file-invoice-dollar me-2 text-success"></i>
                            Invoices
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item">
                                <router-link to="/dashboard">
                                    <i class="fas fa-home"></i> Home
                                </router-link>
                            </li>
                            <li class="breadcrumb-item active">Invoices</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <section class="content">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ formatCurrency(totalAmount) }}</h3>
                                <p>Total Revenue</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ invoices.length }}</h3>
                                <p>Total Invoices</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3>{{ cashCount }}</h3>
                                <p>Cash Payments</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-teal">
                            <div class="inner">
                                <h3>{{ transferCount }}</h3>
                                <p>Bank Transfers</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-university"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Card -->
                <div class="card card-success card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list me-2"></i>Invoice List
                        </h3>
                        <div class="card-tools">
                            <button class="btn btn-success btn-sm" @click="openModal()">
                                <i class="fas fa-plus me-1"></i>New Invoice
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filters -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input
                                        type="text"
                                        class="form-control"
                                        placeholder="Search by invoice ID or member..."
                                        v-model="search"
                                        @keyup.enter="fetchInvoices"
                                    />
                                    <button class="btn btn-outline-secondary" @click="fetchInvoices">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" v-model="filterPayment" @change="fetchInvoices">
                                    <option value="">All Payment Types</option>
                                    <option value="cash">Tiền mặt</option>
                                    <option value="transfer">Chuyển khoản</option>
                                </select>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Invoice ID</th>
                                        <th>Member</th>
                                        <th>Amount</th>
                                        <th>Payment</th>
                                        <th>Period</th>
                                        <th>Created</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="loading">
                                        <td colspan="7" class="text-center py-5">
                                            <div class="spinner-border text-success" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <p class="mt-2 mb-0 text-muted">Loading invoices...</p>
                                        </td>
                                    </tr>
                                    <tr v-else-if="invoices.length === 0">
                                        <td colspan="7" class="text-center py-5">
                                            <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                                            <p class="mb-0 text-muted">No invoices found</p>
                                        </td>
                                    </tr>
                                    <tr v-for="invoice in invoices" :key="invoice.id">
                                        <td>
                                            <span class="badge bg-dark">
                                                <i class="fas fa-hashtag me-1"></i>{{ invoice.invoice_id }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-success rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                                <div>
                                                    <span class="fw-bold">{{ invoice.member?.name || '-' }}</span>
                                                    <br>
                                                    <small class="text-muted">{{ invoice.member?.member_id }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-success fw-bold">
                                                <i class="fas fa-plus-circle me-1"></i>
                                                {{ formatCurrency(invoice.amount) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge" :class="invoice.payment_type === 'cash' ? 'bg-success' : 'bg-info'">
                                                <i :class="invoice.payment_type === 'cash' ? 'fas fa-money-bill' : 'fas fa-university'" class="me-1"></i>
                                                {{ invoice.payment_type === 'cash' ? 'Tiền mặt' : 'Chuyển khoản' }}
                                            </span>
                                        </td>
                                        <td>
                                            <i class="fas fa-calendar-alt me-1 text-muted"></i>
                                            <small>{{ formatDate(invoice.start_date) }} - {{ formatDate(invoice.end_date) }}</small>
                                        </td>
                                        <td>
                                            <i class="fas fa-clock me-1 text-muted"></i>
                                            {{ formatDate(invoice.created_at) }}
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <router-link :to="`/invoices/${invoice.id}`" class="btn btn-sm btn-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </router-link>
                                                <button class="btn btn-sm btn-success" @click="printInvoice(invoice.id)" title="Print">
                                                    <i class="fas fa-print"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot v-if="invoices.length > 0">
                                    <tr class="table-success">
                                        <td colspan="2" class="text-end fw-bold">Total:</td>
                                        <td class="text-success fw-bold">{{ formatCurrency(totalAmount) }}</td>
                                        <td colspan="4"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <nav>
                            <ul class="pagination pagination-sm mb-0 justify-content-center">
                                <li class="page-item" :class="{ disabled: currentPage === 1 }">
                                    <a class="page-link" href="#" @click.prevent="changePage(currentPage - 1)">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                                <li
                                    class="page-item"
                                    v-for="page in visiblePages"
                                    :key="page"
                                    :class="{ active: page === currentPage }"
                                >
                                    <a class="page-link" href="#" @click.prevent="changePage(page)">
                                        {{ page }}
                                    </a>
                                </li>
                                <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                                    <a class="page-link" href="#" @click.prevent="changePage(currentPage + 1)">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </section>

        <!-- New Invoice Modal -->
        <div class="modal fade" id="invoiceModal" tabindex="-1" ref="modalRef">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-file-invoice-dollar me-2"></i>
                            Create New Invoice
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form @submit.prevent="saveInvoice">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-user me-1"></i> Select Member *
                                </label>
                                <select class="form-select" v-model="form.member_id" required>
                                    <option value="">-- Select Member --</option>
                                    <option v-for="member in members" :key="member.id" :value="member.id">
                                        {{ member.member_id }} - {{ member.name }}
                                    </option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-dollar-sign me-1"></i> Amount *
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control" v-model="form.amount" min="0" step="1000" required placeholder="0" />
                                    <span class="input-group-text">VND</span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-credit-card me-1"></i> Payment Type *
                                </label>
                                <select class="form-select" v-model="form.payment_type" required>
                                    <option value="cash">Tiền mặt</option>
                                    <option value="transfer">Chuyển khoản</option>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-calendar-plus me-1"></i> Start Date *
                                    </label>
                                    <input type="date" class="form-control" v-model="form.start_date" required />
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-calendar-times me-1"></i> End Date *
                                    </label>
                                    <input type="date" class="form-control" v-model="form.end_date" required />
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-sticky-note me-1"></i> Note
                                </label>
                                <textarea class="form-control" v-model="form.note" rows="2" placeholder="Optional note"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-success" :disabled="saving">
                                <span v-if="saving">
                                    <i class="fas fa-spinner fa-spin me-1"></i> Creating...
                                </span>
                                <span v-else>
                                    <i class="fas fa-plus me-1"></i> Create Invoice
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, reactive, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';
import { Modal } from 'bootstrap';
import api from '@/utils/axios';

export default {
    name: 'Invoices',
    setup() {
        const router = useRouter();
        const toast = useToast();
        const modalRef = ref(null);
        let modalInstance = null;

        const invoices = ref([]);
        const members = ref([]);
        const loading = ref(false);
        const saving = ref(false);
        const search = ref('');
        const filterPayment = ref('');
        const currentPage = ref(1);
        const totalPages = ref(1);

        const form = reactive({
            member_id: '',
            amount: '',
            payment_type: 'cash',
            start_date: '',
            end_date: '',
            note: '',
        });

        const totalAmount = computed(() => {
            return invoices.value.reduce((sum, inv) => sum + parseFloat(inv.amount || 0), 0);
        });

        const cashCount = computed(() => {
            return invoices.value.filter(inv => inv.payment_type === 'cash').length;
        });

        const transferCount = computed(() => {
            return invoices.value.filter(inv => inv.payment_type === 'transfer').length;
        });

        const visiblePages = computed(() => {
            const pages = [];
            const start = Math.max(1, currentPage.value - 2);
            const end = Math.min(totalPages.value, currentPage.value + 2);
            for (let i = start; i <= end; i++) {
                pages.push(i);
            }
            return pages;
        });

        const resetForm = () => {
            form.member_id = '';
            form.amount = '';
            form.payment_type = 'cash';
            form.start_date = new Date().toISOString().split('T')[0];
            const endDate = new Date();
            endDate.setMonth(endDate.getMonth() + 1);
            form.end_date = endDate.toISOString().split('T')[0];
            form.note = '';
        };

        const fetchInvoices = async () => {
            loading.value = true;
            try {
                const params = { page: currentPage.value };
                if (search.value) params.search = search.value;
                if (filterPayment.value) params.payment_type = filterPayment.value;

                const response = await api.get('/invoices', { params });
                invoices.value = response.data.data.data || [];
                totalPages.value = response.data.data.last_page || 1;
            } catch (error) {
                toast.error('Failed to fetch invoices');
            } finally {
                loading.value = false;
            }
        };

        const fetchMembers = async () => {
            try {
                const response = await api.get('/members?per_page=1000');
                members.value = response.data.data.data || [];
            } catch (error) {
                console.error('Failed to fetch members');
            }
        };

        const openModal = () => {
            resetForm();
            if (!modalInstance) {
                modalInstance = new Modal(modalRef.value);
            }
            modalInstance.show();
        };

        const saveInvoice = async () => {
            saving.value = true;
            try {
                await api.post('/invoices', form);
                toast.success('Invoice created successfully');
                modalInstance.hide();
                fetchInvoices();
            } catch (error) {
                const message = error.response?.data?.message || 'Failed to create invoice';
                toast.error(message);
            } finally {
                saving.value = false;
            }
        };

        const printInvoice = (id) => {
            router.push(`/invoices/${id}`);
        };

        const formatDate = (date) => {
            if (!date) return '-';
            return new Date(date).toLocaleDateString('vi-VN');
        };

        const formatCurrency = (amount) => {
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND',
                maximumFractionDigits: 0,
            }).format(amount);
        };

        const changePage = (page) => {
            if (page >= 1 && page <= totalPages.value) {
                currentPage.value = page;
                fetchInvoices();
            }
        };

        onMounted(() => {
            fetchInvoices();
            fetchMembers();
        });

        return {
            invoices,
            members,
            loading,
            saving,
            search,
            filterPayment,
            currentPage,
            totalPages,
            visiblePages,
            form,
            modalRef,
            totalAmount,
            cashCount,
            transferCount,
            fetchInvoices,
            openModal,
            saveInvoice,
            printInvoice,
            formatDate,
            formatCurrency,
            changePage,
        };
    },
};
</script>

<style scoped>
.small-box {
    border-radius: 0.5rem;
    transition: transform 0.3s ease;
}

.small-box:hover {
    transform: translateY(-3px);
}

.small-box .inner h3 {
    font-size: 1.5rem;
    white-space: nowrap;
}

.bg-teal {
    background-color: #20c997 !important;
    color: white;
}

.avatar-sm {
    font-size: 0.9rem;
}

.btn-group .btn {
    padding: 0.25rem 0.5rem;
}
</style>
