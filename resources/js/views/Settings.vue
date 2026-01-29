<template>
    <div>
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Settings</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><router-link to="/dashboard">Home</router-link></li>
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
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-cog me-2"></i>Gym Information
                                </h3>
                            </div>
                            <form @submit.prevent="saveSettings">
                                <div class="card-body">
                                    <!-- Loading -->
                                    <div v-if="loading" class="text-center py-5">
                                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                                    </div>

                                    <div v-else>
                                        <!-- Logo Upload -->
                                        <div class="mb-4">
                                            <label class="form-label">Gym Logo</label>
                                            <div class="d-flex align-items-center gap-3">
                                                <div v-if="form.gym_logo" class="border rounded p-2">
                                                    <img :src="form.gym_logo" alt="Logo" style="max-height: 80px;" />
                                                </div>
                                                <div v-else class="border rounded p-3 text-muted">
                                                    <i class="fas fa-image fa-2x"></i>
                                                </div>
                                                <div>
                                                    <input 
                                                        type="file" 
                                                        class="form-control" 
                                                        @change="handleLogo" 
                                                        accept="image/*" 
                                                        style="max-width: 300px;"
                                                    />
                                                    <small class="text-muted">Recommended: 200x80px</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Gym Name -->
                                        <div class="mb-3">
                                            <label class="form-label">Gym Name *</label>
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
                                                <label class="form-label">Phone</label>
                                                <input 
                                                    type="text" 
                                                    class="form-control" 
                                                    v-model="form.gym_phone" 
                                                    placeholder="Enter phone number"
                                                />
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Email</label>
                                                <input 
                                                    type="email" 
                                                    class="form-control" 
                                                    v-model="form.gym_email" 
                                                    placeholder="Enter email address"
                                                />
                                            </div>
                                        </div>

                                        <!-- Address -->
                                        <div class="mb-3">
                                            <label class="form-label">Address</label>
                                            <textarea 
                                                class="form-control" 
                                                v-model="form.gym_address" 
                                                rows="2"
                                                placeholder="Enter gym address"
                                            ></textarea>
                                        </div>

                                        <hr />
                                        <h6 class="mb-3"><i class="fas fa-share-alt me-2"></i>Social Links</h6>

                                        <!-- Facebook -->
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i class="fab fa-facebook text-primary me-2"></i>Facebook URL
                                            </label>
                                            <input 
                                                type="url" 
                                                class="form-control" 
                                                v-model="form.facebook_url" 
                                                placeholder="https://facebook.com/yourgym"
                                            />
                                        </div>

                                        <!-- Youtube -->
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i class="fab fa-youtube text-danger me-2"></i>Youtube URL
                                            </label>
                                            <input 
                                                type="url" 
                                                class="form-control" 
                                                v-model="form.youtube_url" 
                                                placeholder="https://youtube.com/c/yourgym"
                                            />
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary" :disabled="saving">
                                        <span v-if="saving">
                                            <i class="fas fa-spinner fa-spin me-2"></i>Saving...
                                        </span>
                                        <span v-else>
                                            <i class="fas fa-save me-2"></i>Save Settings
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Preview -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-eye me-2"></i>Invoice Preview
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <img v-if="form.gym_logo" :src="form.gym_logo" alt="Logo" style="max-height: 60px;" />
                                    <h5 v-else class="text-primary">
                                        <i class="fas fa-dumbbell me-2"></i>{{ form.gym_name || 'GYM Manager' }}
                                    </h5>
                                </div>
                                <div class="small">
                                    <p class="mb-1" v-if="form.gym_address">
                                        <i class="fas fa-map-marker-alt me-2"></i>{{ form.gym_address }}
                                    </p>
                                    <p class="mb-1" v-if="form.gym_phone">
                                        <i class="fas fa-phone me-2"></i>{{ form.gym_phone }}
                                    </p>
                                    <p class="mb-1" v-if="form.gym_email">
                                        <i class="fas fa-envelope me-2"></i>{{ form.gym_email }}
                                    </p>
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
                const reader = new FileReader();
                reader.onload = (e) => {
                    form.gym_logo = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        };

        onMounted(() => {
            fetchSettings();
        });

        return {
            loading, saving, form,
            fetchSettings, saveSettings, handleLogo,
        };
    },
};
</script>
