<template>
    <div>
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">
                            <i class="fas fa-money-bill-wave me-2 text-warning"></i>
                            Expenses
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item">
                                <router-link to="/dashboard">
                                    <i class="fas fa-home"></i> Home
                                </router-link>
                            </li>
                            <li class="breadcrumb-item active">Expenses</li>
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
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ formatCurrency(totalAmount) }}</h3>
                                <p>Total Expenses</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ expenses.length }}</h3>
                                <p>Total Records</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-list"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Card -->
                <div class="card card-warning card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-receipt me-2"></i>Expense List
                        </h3>
                        <div class="card-tools">
                            <button class="btn btn-primary btn-sm" @click="openModal()">
                                <i class="fas fa-plus me-1"></i>Add Expense
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
                        </div>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th>Note</th>
                                        <th>Created By</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="loading">
                                        <td colspan="8" class="text-center py-5">
                                            <div class="spinner-border text-warning" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <p class="mt-2 mb-0 text-muted">Loading expenses...</p>
                                        </td>
                                    </tr>
                                    <tr v-else-if="expenses.length === 0">
                                        <td colspan="8" class="text-center py-5">
                                            <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                                            <p class="mb-0 text-muted">No expenses found</p>
                                        </td>
                                    </tr>
                                    <tr v-for="(expense, index) in expenses" :key="expense.id">
                                        <td>{{ index + 1 }}</td>
                                        <td>
                                            <i class="fas fa-receipt me-2 text-warning"></i>
                                            <span class="fw-bold">{{ expense.name }}</span>
                                        </td>
                                        <td>
                                            <span class="badge" :class="typeBadge(expense.expense_type)">
                                                <i :class="typeIcon(expense.expense_type)" class="me-1"></i>
                                                {{ typeLabel(expense.expense_type) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-danger fw-bold">
                                                <i class="fas fa-minus-circle me-1"></i>
                                                {{ formatCurrency(expense.amount) }}
                                            </span>
                                        </td>
                                        <td>
                                            <i class="fas fa-calendar me-1 text-muted"></i>
                                            {{ formatDate(expense.date) }}
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ expense.note || '-' }}</span>
                                        </td>
                                        <td>{{ expense.user?.name || '-' }}</td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-info" @click="openModal(expense)" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" @click="deleteExpense(expense)" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot v-if="expenses.length > 0">
                                    <tr class="table-warning">
                                        <td colspan="3" class="text-end fw-bold">Total:</td>
                                        <td class="text-danger fw-bold">{{ formatCurrency(totalAmount) }}</td>
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

        <!-- Expense Modal -->
        <div class="modal fade" id="expenseModal" tabindex="-1" ref="modalRef">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title">
                            <i :class="isEdit ? 'fas fa-edit' : 'fas fa-plus-circle'" class="me-2"></i>
                            {{ isEdit ? 'Edit Expense' : 'Add New Expense' }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form @submit.prevent="saveExpense">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-tag me-1"></i> Name *
                                </label>
                                <input type="text" class="form-control" v-model="form.name" required placeholder="Expense name" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-folder me-1"></i> Type *
                                </label>
                                <select class="form-select" v-model="form.expense_type" required>
                                    <option value="utilities">Tiền điện/nước</option>
                                    <option value="equipment">Thiết bị</option>
                                    <option value="salary">Lương nhân viên</option>
                                    <option value="maintenance">Bảo trì</option>
                                    <option value="other">Khác</option>
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
                                    <i class="fas fa-calendar me-1"></i> Date *
                                </label>
                                <input type="date" class="form-control" v-model="form.date" required />
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
                            <button type="submit" class="btn btn-warning" :disabled="saving">
                                <span v-if="saving">
                                    <i class="fas fa-spinner fa-spin me-1"></i> Saving...
                                </span>
                                <span v-else>
                                    <i class="fas fa-save me-1"></i> Save
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
                currency: 'VND',
                maximumFractionDigits: 0,
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
                equipment: 'bg-warning text-dark',
                salary: 'bg-primary',
                maintenance: 'bg-secondary',
                other: 'bg-dark',
            };
            return badges[type] || 'bg-secondary';
        };

        const typeIcon = (type) => {
            const icons = {
                utilities: 'fas fa-bolt',
                equipment: 'fas fa-dumbbell',
                salary: 'fas fa-user-tie',
                maintenance: 'fas fa-wrench',
                other: 'fas fa-ellipsis-h',
            };
            return icons[type] || 'fas fa-tag';
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
            expenses,
            loading,
            saving,
            search,
            filterType,
            currentPage,
            totalPages,
            visiblePages,
            isEdit,
            form,
            modalRef,
            totalAmount,
            fetchExpenses,
            openModal,
            saveExpense,
            deleteExpense,
            formatDate,
            formatCurrency,
            typeLabel,
            typeBadge,
            typeIcon,
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

.btn-group .btn {
    padding: 0.25rem 0.5rem;
}
</style>
