<template>
    <div>
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">
                            <i class="fas fa-users me-2 text-primary"></i>
                            Members
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item">
                                <router-link to="/dashboard">
                                    <i class="fas fa-home"></i> Home
                                </router-link>
                            </li>
                            <li class="breadcrumb-item active">Members</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list me-2"></i>Member List
                        </h3>
                        <div class="card-tools">
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <input
                                    type="text"
                                    class="form-control"
                                    placeholder="Search..."
                                    v-model="search"
                                    @keyup.enter="fetchMembers"
                                />
                                <button class="btn btn-primary" @click="fetchMembers">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                            <span class="text-muted">
                                Showing {{ members.length }} members
                            </span>
                            <button class="btn btn-primary" @click="openModal()">
                                <i class="fas fa-plus me-2"></i>Add Member
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Gender</th>
                                        <th>Mobile</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="loading">
                                        <td colspan="8" class="text-center py-5">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <p class="mt-2 mb-0 text-muted">Loading members...</p>
                                        </td>
                                    </tr>
                                    <tr v-else-if="members.length === 0">
                                        <td colspan="8" class="text-center py-5">
                                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                            <p class="mb-0 text-muted">No members found</p>
                                        </td>
                                    </tr>
                                    <tr v-for="member in members" :key="member.id">
                                        <td>
                                            <span class="badge bg-secondary">{{ member.member_id }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                                <span class="fw-bold">{{ member.name }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge" :class="genderBadge(member.gender)">
                                                <i :class="genderIcon(member.gender)" class="me-1"></i>
                                                {{ capitalizeFirst(member.gender) }}
                                            </span>
                                        </td>
                                        <td>
                                            <i class="fas fa-phone me-1 text-muted"></i>
                                            {{ member.mobile }}
                                        </td>
                                        <td>{{ formatDate(member.start_date) }}</td>
                                        <td>{{ formatDate(member.end_date) }}</td>
                                        <td>
                                            <span class="badge" :class="member.status ? 'bg-success' : 'bg-danger'">
                                                <i :class="member.status ? 'fas fa-check-circle' : 'fas fa-times-circle'" class="me-1"></i>
                                                {{ member.status ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-info" @click="openModal(member)" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" @click="deleteMember(member)" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
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

        <!-- Member Modal -->
        <div class="modal fade" id="memberModal" tabindex="-1" ref="modalRef">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i :class="isEdit ? 'fas fa-edit' : 'fas fa-user-plus'" class="me-2"></i>
                            {{ isEdit ? 'Edit Member' : 'Add New Member' }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form @submit.prevent="saveMember">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-user me-1"></i> Name *
                                    </label>
                                    <input type="text" class="form-control" v-model="form.name" required placeholder="Enter full name" />
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-venus-mars me-1"></i> Gender *
                                    </label>
                                    <select class="form-select" v-model="form.gender" required>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-phone me-1"></i> Mobile *
                                    </label>
                                    <input type="text" class="form-control" v-model="form.mobile" required placeholder="Enter phone number" />
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-tint me-1"></i> Blood Group
                                    </label>
                                    <select class="form-select" v-model="form.blood_group">
                                        <option value="">Select blood group</option>
                                        <option v-for="bg in bloodGroups" :key="bg" :value="bg">{{ bg }}</option>
                                    </select>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-map-marker-alt me-1"></i> Address
                                    </label>
                                    <textarea class="form-control" v-model="form.address" rows="2" placeholder="Enter address"></textarea>
                                </div>
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
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-camera me-1"></i> Photo
                                    </label>
                                    <input type="file" class="form-control" @change="handlePhoto" accept="image/*" />
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-toggle-on me-1"></i> Status
                                    </label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" v-model="form.status" id="statusSwitch" style="width: 3em; height: 1.5em;" />
                                        <label class="form-check-label ms-2" for="statusSwitch">
                                            <span :class="form.status ? 'text-success' : 'text-danger'">
                                                {{ form.status ? 'Active' : 'Inactive' }}
                                            </span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Invoice Section (for new members only) -->
                                <div v-if="!isEdit" class="col-12">
                                    <hr />
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-file-invoice-dollar me-2"></i>Invoice (Optional)
                                    </h6>
                                </div>
                                <div v-if="!isEdit" class="col-md-6 mb-3">
                                    <label class="form-label">Amount</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" v-model="form.amount" min="0" step="1000" placeholder="Enter amount" />
                                        <span class="input-group-text">VND</span>
                                    </div>
                                </div>
                                <div v-if="!isEdit" class="col-md-6 mb-3">
                                    <label class="form-label">Payment Type</label>
                                    <select class="form-select" v-model="form.payment_type">
                                        <option value="cash">Tiền mặt</option>
                                        <option value="transfer">Chuyển khoản</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-primary" :disabled="saving">
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
    name: 'Members',
    setup() {
        const toast = useToast();
        const modalRef = ref(null);
        let modalInstance = null;

        const members = ref([]);
        const loading = ref(false);
        const saving = ref(false);
        const search = ref('');
        const currentPage = ref(1);
        const totalPages = ref(1);
        const isEdit = ref(false);
        const editingId = ref(null);

        const bloodGroups = ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'];

        const form = reactive({
            name: '',
            gender: 'male',
            mobile: '',
            blood_group: '',
            address: '',
            photo: '',
            start_date: '',
            end_date: '',
            status: true,
            amount: '',
            payment_type: 'cash',
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
            form.gender = 'male';
            form.mobile = '';
            form.blood_group = '';
            form.address = '';
            form.photo = '';
            form.start_date = '';
            form.end_date = '';
            form.status = true;
            form.amount = '';
            form.payment_type = 'cash';
            isEdit.value = false;
            editingId.value = null;
        };

        const fetchMembers = async () => {
            loading.value = true;
            try {
                const params = { page: currentPage.value };
                if (search.value) params.search = search.value;

                const response = await api.get('/members', { params });
                members.value = response.data.data.data || [];
                totalPages.value = response.data.data.last_page || 1;
            } catch (error) {
                toast.error('Failed to fetch members');
            } finally {
                loading.value = false;
            }
        };

        const openModal = (member = null) => {
            resetForm();
            if (member) {
                isEdit.value = true;
                editingId.value = member.id;
                form.name = member.name;
                form.gender = member.gender;
                form.mobile = member.mobile;
                form.blood_group = member.blood_group || '';
                form.address = member.address || '';
                form.start_date = member.start_date?.split('T')[0] || '';
                form.end_date = member.end_date?.split('T')[0] || '';
                form.status = member.status;
            }
            if (!modalInstance) {
                modalInstance = new Modal(modalRef.value);
            }
            modalInstance.show();
        };

        const saveMember = async () => {
            saving.value = true;
            try {
                const data = { ...form };
                if (!data.photo) delete data.photo;

                if (isEdit.value) {
                    await api.put(`/members/${editingId.value}`, data);
                    toast.success('Member updated successfully');
                } else {
                    await api.post('/members', data);
                    toast.success('Member created successfully');
                }

                modalInstance.hide();
                fetchMembers();
            } catch (error) {
                const message = error.response?.data?.message || 'Failed to save member';
                toast.error(message);
            } finally {
                saving.value = false;
            }
        };

        const deleteMember = async (member) => {
            const result = await Swal.fire({
                title: 'Are you sure?',
                text: `Delete member "${member.name}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
            });

            if (result.isConfirmed) {
                try {
                    await api.delete(`/members/${member.id}`);
                    toast.success('Member deleted successfully');
                    fetchMembers();
                } catch (error) {
                    toast.error('Failed to delete member');
                }
            }
        };

        const handlePhoto = (event) => {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    form.photo = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        };

        const formatDate = (date) => {
            if (!date) return '-';
            return new Date(date).toLocaleDateString('vi-VN');
        };

        const genderBadge = (gender) => {
            const badges = { male: 'bg-primary', female: 'bg-pink', other: 'bg-secondary' };
            return badges[gender] || 'bg-secondary';
        };

        const genderIcon = (gender) => {
            const icons = { male: 'fas fa-mars', female: 'fas fa-venus', other: 'fas fa-genderless' };
            return icons[gender] || 'fas fa-genderless';
        };

        const capitalizeFirst = (str) => {
            return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
        };

        const changePage = (page) => {
            if (page >= 1 && page <= totalPages.value) {
                currentPage.value = page;
                fetchMembers();
            }
        };

        onMounted(() => {
            fetchMembers();
        });

        return {
            members,
            loading,
            saving,
            search,
            currentPage,
            totalPages,
            visiblePages,
            isEdit,
            form,
            bloodGroups,
            modalRef,
            fetchMembers,
            openModal,
            saveMember,
            deleteMember,
            handlePhoto,
            formatDate,
            genderBadge,
            genderIcon,
            capitalizeFirst,
            changePage,
        };
    },
};
</script>

<style scoped>
.bg-pink {
    background-color: #e83e8c !important;
}

.avatar-sm {
    font-size: 0.9rem;
}

.table th {
    font-weight: 600;
    white-space: nowrap;
}

.btn-group .btn {
    padding: 0.25rem 0.5rem;
}

.modal-header {
    border-radius: 0;
}

.form-switch .form-check-input {
    cursor: pointer;
}

.card-tools .input-group {
    flex-wrap: nowrap;
}
</style>
