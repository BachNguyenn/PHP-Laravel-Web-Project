<template>
    <div>
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Expenses</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><router-link to="/dashboard">Home</router-link></li>
                            <li class="breadcrumb-item active">Expenses</li>
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
                                        placeholder="Search expenses..."
                                        v-model="search"
                                        @keyup.enter="fetchExpenses"
                                    />
                                    <button class="btn btn-outline-secondary" @click="fetchExpenses">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" v-model="filterType" @change="fetchExpenses">
                                    <option value="">All Types</option>
                                    <option value="utilities">Tiền điện/nước</option>
                                    <option value="equipment">Thiết bị</option>
                                    <option value="salary">Lương nhân viên</option>
                                    <option value="maintenance">Bảo trì</option>
                                    <option value="other">Khác</option>
                                </select>
                            </div>
                            <div class="col-md-5 text-end">
                                <button class="btn btn-primary" @click="openModal()">
                                    <i class="fas fa-plus me-2"></i>Add Expense
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Created By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="loading">
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                                    </td>
                                </tr>
                                <tr v-else-if="expenses.length === 0">
                                    <td colspan="7" class="text-center py-4">No expenses found</td>
                                </tr>
                                <tr v-for="(expense, index) in expenses" :key="expense.id">
                                    <td>{{ index + 1 }}</td>
                                    <td>{{ expense.name }}</td>
                                    <td>
                                        <span class="badge" :class="typeBadge(expense.expense_type)">
                                            {{ typeLabel(expense.expense_type) }}
                                        </span>
                                    </td>
                                    <td class="text-danger fw-bold">{{ formatCurrency(expense.amount) }}</td>
                                    <td>{{ formatDate(expense.date) }}</td>
                                    <td>{{ expense.user?.name || '-' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info me-1" @click="openModal(expense)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" @click="deleteExpense(expense)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot v-if="expenses.length > 0">
                                <tr class="bg-light">
                                    <td colspan="3" class="text-end fw-bold">Total:</td>
                                    <td class="text-danger fw-bold">{{ formatCurrency(totalAmount) }}</td>
                                    <td colspan="3"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="card-footer">
                        <nav>
                            <ul class="pagination mb-0 justify-content-center">
                                <li class="page-item" :class="{ disabled: currentPage === 1 }">
                                    <a class="page-link" href="#" @click.prevent="changePage(currentPage - 1)">
                                        Previous
                                    </a>
                                </li>
                                <li class="page-item" v-for="page in totalPages" :key="page" 
                                    :class="{ active: page === currentPage }">
                                    <a class="page-link" href="#" @click.prevent="changePage(page)">
                                        {{ page }}
                                    </a>
                                </li>
                                <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                                    <a class="page-link" href="#" @click.prevent="changePage(currentPage + 1)">
                                        Next
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </section>

        <!-- Expense Modal -->
        <div class="modal fade" id="expenseModal" tabindex="-1" ref="modalRef">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ isEdit ? 'Edit Expense' : 'Add New Expense' }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form @submit.prevent="saveExpense">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Name *</label>
                                <input type="text" class="form-control" v-model="form.name" required />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Type *</label>
                                <select class="form-select" v-model="form.expense_type" required>
                                    <option value="utilities">Tiền điện/nước</option>
                                    <option value="equipment">Thiết bị</option>
                                    <option value="salary">Lương nhân viên</option>
                                    <option value="maintenance">Bảo trì</option>
                                    <option value="other">Khác</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Amount *</label>
                                <input type="number" class="form-control" v-model="form.amount" min="0" step="1000" required />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Date *</label>
                                <input type="date" class="form-control" v-model="form.date" required />
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
                                    <i class="fas fa-save me-2"></i>Save
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
import { useToast } from 'vue-toastification';
import Swal from 'sweetalert2';
import { Modal } from 'bootstrap';
import api from '@/utils/axios';

export default {
    name: 'Expenses',
    setup() {
        const toast = useToast();
        const modalRef = ref(null);
        let modalInstance = null;

        const expenses = ref([]);
        const loading = ref(false);
        const saving = ref(false);
        const search = ref('');
        const filterType = ref('');
        const currentPage = ref(1);
        const totalPages = ref(1);
        const isEdit = ref(false);
        const editingId = ref(null);

        const form = reactive({
            name: '',
            expense_type: 'other',
            amount: '',
            date: '',
            note: '',
        });

        const totalAmount = computed(() => {
            return expenses.value.reduce((sum, exp) => sum + parseFloat(exp.amount || 0), 0);
        });

        const resetForm = () => {
            form.name = '';
            form.expense_type = 'other';
            form.amount = '';
            form.date = new Date().toISOString().split('T')[0];
            form.note = '';
            isEdit.value = false;
            editingId.value = null;
        };

        const fetchExpenses = async () => {
            loading.value = true;
            try {
                const params = { page: currentPage.value };
                if (search.value) params.search = search.value;
                if (filterType.value) params.expense_type = filterType.value;
                
                const response = await api.get('/expenses', { params });
                expenses.value = response.data.data.data || [];
                totalPages.value = response.data.data.last_page || 1;
            } catch (error) {
                toast.error('Failed to fetch expenses');
            } finally {
                loading.value = false;
            }
        };

        const openModal = (expense = null) => {
            resetForm();
            if (expense) {
                isEdit.value = true;
                editingId.value = expense.id;
                form.name = expense.name;
                form.expense_type = expense.expense_type;
                form.amount = expense.amount;
                form.date = expense.date?.split('T')[0] || '';
                form.note = expense.note || '';
            }
            if (!modalInstance) {
                modalInstance = new Modal(modalRef.value);
            }
            modalInstance.show();
        };

        const saveExpense = async () => {
            saving.value = true;
            try {
                if (isEdit.value) {
                    await api.put(`/expenses/${editingId.value}`, form);
                    toast.success('Expense updated successfully');
                } else {
                    await api.post('/expenses', form);
                    toast.success('Expense created successfully');
                }
                
                modalInstance.hide();
                fetchExpenses();
            } catch (error) {
                const message = error.response?.data?.message || 'Failed to save expense';
                toast.error(message);
            } finally {
                saving.value = false;
            }
        };

        const deleteExpense = async (expense) => {
            const result = await Swal.fire({
                title: 'Are you sure?',
                text: `Delete expense "${expense.name}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
            });

            if (result.isConfirmed) {
                try {
                    await api.delete(`/expenses/${expense.id}`);
                    toast.success('Expense deleted successfully');
                    fetchExpenses();
                } catch (error) {
                    toast.error('Failed to delete expense');
                }
            }
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

        const typeLabel = (type) => {
            const labels = {
                utilities: 'Tiền điện/nước',
                equipment: 'Thiết bị',
                salary: 'Lương nhân viên',
                maintenance: 'Bảo trì',
                other: 'Khác',
            };
            return labels[type] || type;
        };

        const typeBadge = (type) => {
            const badges = {
                utilities: 'bg-info',
                equipment: 'bg-warning',
                salary: 'bg-primary',
                maintenance: 'bg-secondary',
                other: 'bg-dark',
            };
            return badges[type] || 'bg-secondary';
        };

        const changePage = (page) => {
            if (page >= 1 && page <= totalPages.value) {
                currentPage.value = page;
                fetchExpenses();
            }
        };

        onMounted(() => {
            fetchExpenses();
        });

        return {
            expenses, loading, saving, search, filterType,
            currentPage, totalPages, isEdit, form, modalRef, totalAmount,
            fetchExpenses, openModal, saveExpense, deleteExpense,
            formatDate, formatCurrency, typeLabel, typeBadge, changePage,
        };
    },
};
</script>
