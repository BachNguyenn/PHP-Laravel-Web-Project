<template>
    <div>
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">
                            <i class="fas fa-cog me-2 text-info"></i>
                            Settings
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item">
                                <router-link to="/dashboard">
                                    <i class="fas fa-home"></i> Home
                                </router-link>
                            </li>
                            <li class="breadcrumb-item active">Settings</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <!-- Settings Form -->
                    <div class="col-lg-8">
                        <div class="card card-info card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-building me-2"></i>Gym Information
                                </h3>
                            </div>
                            <form @submit.prevent="saveSettings">
                                <div class="card-body">
                                    <!-- Loading -->
                                    <div v-if="loading" class="text-center py-5">
                                        <div class="spinner-border text-info" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-2 text-muted">Loading settings...</p>
                                    </div>

                                    <div v-else>
                                        <!-- Logo Upload -->
                                        <div class="mb-4">
                                            <label class="form-label fw-bold">
                                                <i class="fas fa-image me-1"></i> Gym Logo
                                            </label>
                                            <div class="d-flex align-items-center gap-3">
                                                <div v-if="form.gym_logo" class="logo-preview">
                                                    <img :src="form.gym_logo" alt="Logo" />
                                                    <button type="button" class="btn-remove" @click="removeLogo" title="Remove logo">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                                <div v-else class="logo-placeholder">
                                                    <i class="fas fa-image fa-2x text-muted"></i>
                                                    <p class="mb-0 text-muted small">No logo</p>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <input
                                                        type="file"
                                                        class="form-control"
                                                        @change="handleLogo"
                                                        accept="image/*"
                                                    />
                                                    <small class="text-muted">
                                                        <i class="fas fa-info-circle me-1"></i>Recommended: 200x80px
                                                    </small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Gym Name -->
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">
                                                <i class="fas fa-building me-1"></i> Gym Name *
                                            </label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                v-model="form.gym_name"
                                                placeholder="Enter gym name"
                                                required
                                            />
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">
                                                    <i class="fas fa-phone me-1"></i> Phone
                                                </label>
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    v-model="form.gym_phone"
                                                    placeholder="Enter phone number"
                                                />
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">
                                                    <i class="fas fa-envelope me-1"></i> Email
                                                </label>
                                                <input
                                                    type="email"
                                                    class="form-control"
                                                    v-model="form.gym_email"
                                                    placeholder="Enter email address"
                                                />
                                            </div>
                                        </div>

                                        <!-- Address -->
                                        <div class="mb-4">
                                            <label class="form-label fw-bold">
                                                <i class="fas fa-map-marker-alt me-1"></i> Address
                                            </label>
                                            <textarea
                                                class="form-control"
                                                v-model="form.gym_address"
                                                rows="2"
                                                placeholder="Enter gym address"
                                            ></textarea>
                                        </div>

                                        <hr class="my-4" />

                                        <!-- Social Links Section -->
                                        <h5 class="mb-3">
                                            <i class="fas fa-share-alt me-2 text-info"></i>Social Links
                                        </h5>

                                        <!-- Facebook -->
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i class="fab fa-facebook text-primary me-2"></i>Facebook URL
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="fab fa-facebook-f text-primary"></i>
                                                </span>
                                                <input
                                                    type="url"
                                                    class="form-control"
                                                    v-model="form.facebook_url"
                                                    placeholder="https://facebook.com/yourgym"
                                                />
                                            </div>
                                        </div>

                                        <!-- Youtube -->
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i class="fab fa-youtube text-danger me-2"></i>Youtube URL
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="fab fa-youtube text-danger"></i>
                                                </span>
                                                <input
                                                    type="url"
                                                    class="form-control"
                                                    v-model="form.youtube_url"
                                                    placeholder="https://youtube.com/c/yourgym"
                                                />
                                            </div>
                                        </div>

                                        <!-- Instagram -->
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i class="fab fa-instagram text-danger me-2"></i>Instagram URL
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="fab fa-instagram" style="color: #e4405f;"></i>
                                                </span>
                                                <input
                                                    type="url"
                                                    class="form-control"
                                                    v-model="form.instagram_url"
                                                    placeholder="https://instagram.com/yourgym"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-light">
                                    <button type="submit" class="btn btn-info" :disabled="saving">
                                        <span v-if="saving">
                                            <i class="fas fa-spinner fa-spin me-2"></i>Saving...
                                        </span>
                                        <span v-else>
                                            <i class="fas fa-save me-2"></i>Save Settings
                                        </span>
                                    </button>
                                    <button type="button" class="btn btn-secondary ms-2" @click="fetchSettings">
                                        <i class="fas fa-undo me-2"></i>Reset
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Preview -->
                    <div class="col-lg-4">
                        <!-- Invoice Preview Card -->
                        <div class="card card-success card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-file-invoice me-2"></i>Invoice Preview
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="invoice-preview p-3 border rounded bg-white">
                                    <div class="text-center mb-3">
                                        <img v-if="form.gym_logo" :src="form.gym_logo" alt="Logo" class="preview-logo" />
                                        <div v-else class="preview-logo-placeholder">
                                            <i class="fas fa-dumbbell fa-2x text-primary"></i>
                                        </div>
                                        <h5 class="text-primary mt-2 mb-0">{{ form.gym_name || 'GYM Manager' }}</h5>
                                    </div>
                                    <hr />
                                    <div class="small text-muted">
                                        <p class="mb-1" v-if="form.gym_address">
                                            <i class="fas fa-map-marker-alt me-2 text-info"></i>{{ form.gym_address }}
                                        </p>
                                        <p class="mb-1" v-if="form.gym_phone">
                                            <i class="fas fa-phone me-2 text-info"></i>{{ form.gym_phone }}
                                        </p>
                                        <p class="mb-1" v-if="form.gym_email">
                                            <i class="fas fa-envelope me-2 text-info"></i>{{ form.gym_email }}
                                        </p>
                                    </div>
                                    <div v-if="form.facebook_url || form.youtube_url || form.instagram_url" class="mt-3 pt-3 border-top">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a v-if="form.facebook_url" :href="form.facebook_url" class="btn btn-sm btn-outline-primary" target="_blank">
                                                <i class="fab fa-facebook"></i>
                                            </a>
                                            <a v-if="form.youtube_url" :href="form.youtube_url" class="btn btn-sm btn-outline-danger" target="_blank">
                                                <i class="fab fa-youtube"></i>
                                            </a>
                                            <a v-if="form.instagram_url" :href="form.instagram_url" class="btn btn-sm btn-outline-danger" target="_blank" style="border-color: #e4405f; color: #e4405f;">
                                                <i class="fab fa-instagram"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tips Card -->
                        <div class="card card-warning card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-lightbulb me-2"></i>Tips
                                </h3>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <small>Use a high-quality logo for better print results</small>
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <small>Add all contact info for professional invoices</small>
                                    </li>
                                    <li class="mb-0">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <small>Social links will appear on invoice footer</small>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
import { ref, reactive, onMounted } from 'vue';
import { useToast } from 'vue-toastification';
import api from '@/utils/axios';

export default {
    name: 'Settings',
    setup() {
        const toast = useToast();

        const loading = ref(true);
        const saving = ref(false);

        const form = reactive({
            gym_name: '',
            gym_logo: '',
            gym_address: '',
            gym_phone: '',
            gym_email: '',
            facebook_url: '',
            youtube_url: '',
            instagram_url: '',
        });

        const fetchSettings = async () => {
            loading.value = true;
            try {
                const response = await api.get('/settings');
                const settings = response.data.data;

                form.gym_name = settings.gym_name || '';
                form.gym_logo = settings.gym_logo || '';
                form.gym_address = settings.gym_address || '';
                form.gym_phone = settings.gym_phone || '';
                form.gym_email = settings.gym_email || '';
                form.facebook_url = settings.facebook_url || '';
                form.youtube_url = settings.youtube_url || '';
                form.instagram_url = settings.instagram_url || '';
            } catch (error) {
                toast.error('Failed to fetch settings');
            } finally {
                loading.value = false;
            }
        };

        const saveSettings = async () => {
            saving.value = true;
            try {
                await api.put('/settings', form);
                toast.success('Settings saved successfully');
            } catch (error) {
                toast.error('Failed to save settings');
            } finally {
                saving.value = false;
            }
        };

        const handleLogo = (event) => {
            const file = event.target.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    toast.error('Logo size should be less than 2MB');
                    return;
                }
                const reader = new FileReader();
                reader.onload = (e) => {
                    form.gym_logo = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        };

        const removeLogo = () => {
            form.gym_logo = '';
        };

        onMounted(() => {
            fetchSettings();
        });

        return {
            loading,
            saving,
            form,
            fetchSettings,
            saveSettings,
            handleLogo,
            removeLogo,
        };
    },
};
</script>

<style scoped>
.logo-preview {
    position: relative;
    border: 2px solid #dee2e6;
    border-radius: 0.5rem;
    padding: 0.5rem;
    background: #fff;
}

.logo-preview img {
    max-height: 80px;
    display: block;
}

.logo-preview .btn-remove {
    position: absolute;
    top: -8px;
    right: -8px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #dc3545;
    color: white;
    border: none;
    font-size: 0.75rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.logo-preview .btn-remove:hover {
    background: #bb2d3b;
}

.logo-placeholder {
    width: 100px;
    height: 80px;
    border: 2px dashed #dee2e6;
    border-radius: 0.5rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
}

.invoice-preview {
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.preview-logo {
    max-height: 60px;
}

.preview-logo-placeholder {
    width: 60px;
    height: 60px;
    margin: 0 auto;
    border-radius: 50%;
    background: #e3f2fd;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
