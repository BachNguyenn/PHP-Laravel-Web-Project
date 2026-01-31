<template>
    <div class="login-page">
        <div class="login-box">
            <!-- Logo -->
            <div class="text-center mb-4">
                <h1 class="text-white">
                    <i class="fas fa-dumbbell me-2"></i>
                    GYM Manager
                </h1>
                <p class="text-white-50">Admin Panel</p>
            </div>

            <!-- Login Card -->
            <div class="card card-outline card-primary">
                <div class="card-header text-center">
                    <h4 class="mb-0">
                        <i class="fas fa-sign-in-alt me-2"></i>Sign In
                    </h4>
                </div>
                <div class="card-body login-card-body">
                    <p class="login-box-msg text-muted">Sign in to start your session</p>

                    <form @submit.prevent="login">
                        <!-- Email -->
                        <div class="input-group mb-3">
                            <input 
                                type="email" 
                                class="form-control" 
                                :class="{ 'is-invalid': errors.email }"
                                placeholder="Email"
                                v-model="form.email"
                                required
                            />
                            <span class="input-group-text">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <div v-if="errors.email" class="invalid-feedback">
                                {{ errors.email }}
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="input-group mb-3">
                            <input 
                                :type="showPassword ? 'text' : 'password'" 
                                class="form-control" 
                                :class="{ 'is-invalid': errors.password }"
                                placeholder="Password"
                                v-model="form.password"
                                required
                            />
                            <span class="input-group-text" style="cursor: pointer;" @click="showPassword = !showPassword">
                                <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                            </span>
                            <div v-if="errors.password" class="invalid-feedback">
                                {{ errors.password }}
                            </div>
                        </div>

                        <!-- Remember Me -->
                        <div class="row mb-3">
                            <div class="col-8">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" v-model="form.remember" id="remember">
                                    <label class="form-check-label" for="remember">
                                        Remember Me
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Error Alert -->
                        <div v-if="errorMessage" class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            {{ errorMessage }}
                            <button type="button" class="btn-close" @click="errorMessage = ''"></button>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg" :disabled="loading">
                                <span v-if="loading">
                                    <i class="fas fa-spinner fa-spin me-2"></i>
                                    Signing in...
                                </span>
                                <span v-else>
                                    <i class="fas fa-sign-in-alt me-2"></i>
                                    Sign In
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-3">
                <p class="text-white-50 small">
                    &copy; 2026 GYM Manager. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';
import api from '@/utils/axios';

export default {
    name: 'Login',
    setup() {
        const router = useRouter();
        const toast = useToast();

        const form = reactive({
            email: '',
            password: '',
            remember: false,
        });

        const errors = reactive({
            email: '',
            password: '',
        });

        const loading = ref(false);
        const showPassword = ref(false);
        const errorMessage = ref('');

        const login = async () => {
            // Reset errors
            errors.email = '';
            errors.password = '';
            errorMessage.value = '';
            loading.value = true;

            try {
                const response = await api.post('/login', {
                    email: form.email,
                    password: form.password,
                });

                // Laravel helper send_response wraps everything in a 'data' property
                const { token, user } = response.data.data;

                // Store token and user info
                localStorage.setItem('access_token', token);
                localStorage.setItem('user', JSON.stringify(user));

                toast.success(`Welcome back, ${user.name}!`);
                router.push('/dashboard');
            } catch (error) {
                if (error.response?.status === 422) {
                    const validationErrors = error.response.data.errors || {};
                    errors.email = validationErrors.email?.[0] || '';
                    errors.password = validationErrors.password?.[0] || '';
                } else if (error.response?.status === 401) {
                    errorMessage.value = 'Invalid email or password';
                } else {
                    errorMessage.value = error.response?.data?.message || 'An error occurred. Please try again.';
                }
            } finally {
                loading.value = false;
            }
        };

        return {
            form,
            errors,
            loading,
            showPassword,
            errorMessage,
            login,
        };
    },
};
</script>

<style scoped>
.login-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 1rem;
}

.login-box {
    width: 100%;
    max-width: 420px;
}

.card {
    border-radius: 1rem;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
}

.card-header {
    border-radius: 1rem 1rem 0 0 !important;
    padding: 1.5rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.card-body {
    padding: 2rem;
}

.login-box-msg {
    margin-bottom: 1.5rem;
}

.input-group-text {
    background-color: #f8f9fa;
    border-left: none;
}

.form-control:focus + .input-group-text {
    border-color: #86b7fe;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.btn-primary:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

.btn-primary:disabled {
    opacity: 0.7;
}
</style>
