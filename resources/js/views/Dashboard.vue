<template>
    <div>
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Dashboard</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Info boxes -->
                <div class="row">
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-primary elevation-1">
                                <i class="fas fa-users"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Members</span>
                                <span class="info-box-number">{{ stats.totalMembers }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-success elevation-1">
                                <i class="fas fa-user-check"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Active Members</span>
                                <span class="info-box-number">{{ stats.activeMembers }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-danger elevation-1">
                                <i class="fas fa-user-times"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Inactive Members</span>
                                <span class="info-box-number">{{ stats.inactiveMembers }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Welcome Card -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-dumbbell me-2"></i>
                                    Welcome to GYM Manager
                                </h3>
                            </div>
                            <div class="card-body">
                                <p>Manage your gym members efficiently with this admin panel.</p>
                                <router-link to="/members" class="btn btn-primary">
                                    <i class="fas fa-users me-2"></i>View Members
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import api from '@/utils/axios';

export default {
    name: 'Dashboard',
    setup() {
        const stats = ref({
            totalMembers: 0,
            activeMembers: 0,
            inactiveMembers: 0,
        });

        const fetchStats = async () => {
            try {
                // Fetch all members to calculate stats
                const response = await api.get('/members?per_page=1000');
                const members = response.data.data.data || [];
                
                stats.value.totalMembers = members.length;
                stats.value.activeMembers = members.filter(m => m.status).length;
                stats.value.inactiveMembers = members.filter(m => !m.status).length;
            } catch (error) {
                console.error('Failed to fetch stats:', error);
            }
        };

        onMounted(() => {
            fetchStats();
        });

        return { stats };
    },
};
</script>

<style scoped>
.info-box-number {
    font-size: 1.5rem;
    font-weight: bold;
}
</style>
