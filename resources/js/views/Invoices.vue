<template>
    <div>
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Invoices</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><router-link to="/dashboard">Home</router-link></li>
                            <li class="breadcrumb-item active">Invoices</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
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
                            <div class="col-md-5 text-end">
                                <button class="btn btn-primary" @click="openModal()">
                                    <i class="fas fa-plus me-2"></i>New Invoice
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>Invoice ID</th>
                                    <th>Member</th>
                                    <th>Amount</th>
                                    <th>Payment</th>
                                    <th>Period</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="loading">
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                                    </td>
                                </tr>
                                <tr v-else-if="invoices.length === 0">
                                    <td colspan="7" class="text-center py-4">No invoices found</td>
                                </tr>
                                <tr v-for="invoice in invoices" :key="invoice.id">
                                    <td><strong>{{ invoice.invoice_id }}</strong></td>
                                    <td>
                                        <div>{{ invoice.member?.name || '-' }}</div>
                                        <small class="text-muted">{{ invoice.member?.member_id }}</small>
                                    </td>
                                    <td class="text-success fw-bold">{{ formatCurrency(invoice.amount) }}</td>
                                    <td>
                                        <span class="badge" :class="invoice.payment_type === 'cash' ? 'bg-success' : 'bg-info'">
                                            {{ invoice.payment_type === 'cash' ? 'Tiền mặt' : 'Chuyển khoản' }}
                                        </span>
                                    </td>
                                    <td>
                                        <small>{{ formatDate(invoice.start_date) }} - {{ formatDate(invoice.end_date) }}</small>
                                    </td>
                                    <td>{{ formatDate(invoice.created_at) }}</td>
                                    <td>
                                        <router-link :to="`/invoices/${invoice.id}`" class="btn btn-sm btn-info me-1">
                                            <i class="fas fa-eye"></i>
                                        </router-link>
                                        <button class="btn btn-sm btn-success" @click="printInvoice(invoice.id)">
                                            <i class="fas fa-print"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot v-if="invoices.length > 0">
                                <tr class="bg-light">
                                    <td colspan="2" class="text-end fw-bold">Total:</td>
                                    <td class="text-success fw-bold">{{ formatCurrency(totalAmount) }}</td>
                                    <td colspan="4"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="card-footer">
                        <nav>
                            <ul class="pagination mb-0 justify-content-center">
                                <li class="page-item" :class="{ disabled: currentPage === 1 }">
                                    <a class="page-link" href="#" @click.prevent="changePage(currentPage - 1)">Previous</a>
                                </li>
                                <li class="page-item" v-for="page in totalPages" :key="page" 
                                    :class="{ active: page === currentPage }">
                                    <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a>
                                </li>
                                <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                                    <a class="page-link" href="#" @click.prevent="changePage(currentPage + 1)">Next</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </section>

        <!-- New Invoice Modal -->
        <div class="modal fade" id="invoiceModal" tabindex="-1" ref="modalRef">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Create New Invoice</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form @submit.prevent="saveInvoice">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Select Member *</label>
                                <select class="form-select" v-model="form.member_id" required>
                                    <option value="">-- Select Member --</option>
                                    <option v-for="member in members" :key="member.id" :value="member.id">
                                        {{ member.member_id }} - {{ member.name }}
                                    </option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Amount *</label>
                                <input type="number" class="form-control" v-model="form.amount" min="0" step="1000" required />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Payment Type *</label>
                                <select class="form-select" v-model="form.payment_type" required>
                                    <option value="cash">Tiền mặt</option>
                                    <option value="transfer">Chuyển khoản</option>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Start Date *</label>
                                    <input type="date" class="form-control" v-model="form.start_date" required />
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">End Date *</label>
                                    <input type="date" class="form-control" v-model="form.end_date" required />
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Note</label>
                                <textarea class="form-control" v-model="form.note" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" :disabled="saving">
                                <span v-if="saving">
                                    <i class="fas fa-spinner fa-spin me-2"></i>Saving...
                                </span>
                                <span v-else>
                                    <i class="fas fa-save me-2"></i>Create Invoice
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
                currency: 'VND' 
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
            invoices, members, loading, saving, search, filterPayment,
            currentPage, totalPages, form, modalRef, totalAmount,
            fetchInvoices, openModal, saveInvoice, printInvoice,
            formatDate, formatCurrency, changePage,
        };
    },
};
</script>
