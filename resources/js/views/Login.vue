<template>
    <div class="login-page" style="min-height: 100vh;">
        <div class="login-box">
            <div class="card card-outline card-primary">
                <div class="card-header text-center">
                    <a href="/" class="h1">
                        <i class="fas fa-dumbbell text-primary"></i>
                        <b class="text-primary">GYM</b> Manager
                    </a>
                </div>
                <div class="card-body">
                    <p class="login-box-msg">Sign in to start your session</p>

                    <form @submit.prevent="handleLogin">
                        <div class="input-group mb-3">
                            <input 
                                type="email" 
                                class="form-control" 
                                :class="{ 'is-invalid': errors.email }"
                                placeholder="Email"
                                v-model="form.email"
                                required
                            />
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                            <div class="invalid-feedback" v-if="errors.email">
                                {{ errors.email[0] }}
                            </div>
                        </div>

                        <div class="input-group mb-3">
                            <input 
                                type="password" 
                                class="form-control"
                                :class="{ 'is-invalid': errors.password }"
                                placeholder="Password"
                                v-model="form.password"
                                required
                            />
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                            <div class="invalid-feedback" v-if="errors.password">
                                {{ errors.password[0] }}
                            </div>
                        </div>

                        <div class="alert alert-danger" v-if="errorMessage">
                            {{ errorMessage }}
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button 
                                    type="submit" 
                                    class="btn btn-primary btn-block w-100"
                                    :disabled="loading"
                                >
                                    <span v-if="loading">
                                        <i class="fas fa-spinner fa-spin me-2"></i>Signing in...
                                    </span>
                                    <span v-else>
                                        <i class="fas fa-sign-in-alt me-2"></i>Sign In
                                    </span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';
import axios from 'axios';

export default {
    name: 'Login',
    setup() {
        const router = useRouter();
        const toast = useToast();

        const form = reactive({
            email: '',
            password: '',
        });

        const loading = ref(false);
        const errorMessage = ref('');
        const errors = ref({});

        const handleLogin = async () => {
            loading.value = true;
            errorMessage.value = '';
            errors.value = {};

            try {
                const response = await axios.post('/api/login', form);
                
                if (response.data.status) {
                    // Save token and user to localStorage
                    localStorage.setItem('access_token', response.data.data.token);
                    localStorage.setItem('user', JSON.stringify(response.data.data.user));
                    
                    toast.success('Login successful!');
                    router.push('/dashboard');
                } else {
                    errorMessage.value = response.data.message || 'Login failed';
                }
            } catch (error) {
                if (error.response) {
                    if (error.response.status === 422) {
                        errors.value = error.response.data.errors || {};
                    } else if (error.response.status === 401) {
                        errorMessage.value = 'Invalid email or password';
                    } else {
                        errorMessage.value = error.response.data.message || 'An error occurred';
                    }
                } else {
                    errorMessage.value = 'Network error. Please try again.';
                }
            } finally {
                loading.value = false;
            }
        };

        return { form, loading, errorMessage, errors, handleLogin };
    },
};
</script>

<style scoped>
.login-page {
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
.login-box {
    width: 400px;
}
.card {
    border-radius: 10px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
}
</style>
