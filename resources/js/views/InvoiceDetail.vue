<template>
    <div>
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">
                            <i class="fas fa-file-invoice me-2 text-primary"></i>
                            Invoice Detail
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item">
                                <router-link to="/dashboard">
                                    <i class="fas fa-home"></i> Home
                                </router-link>
                            </li>
                            <li class="breadcrumb-item">
                                <router-link to="/invoices">Invoices</router-link>
                            </li>
                            <li class="breadcrumb-item active">Detail</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <!-- Action buttons -->
                        <div class="mb-3 no-print">
                            <div class="btn-group">
                                <router-link to="/invoices" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Back to List
                                </router-link>
                                <button class="btn btn-primary" @click="printInvoice">
                                    <i class="fas fa-print me-2"></i>Print
                                </button>
                                <button class="btn btn-danger" @click="downloadPDF" :disabled="downloading">
                                    <span v-if="downloading">
                                        <i class="fas fa-spinner fa-spin me-2"></i>Generating...
                                    </span>
                                    <span v-else>
                                        <i class="fas fa-file-pdf me-2"></i>Download PDF
                                    </span>
                                </button>
                            </div>
                        </div>

                        <!-- Loading -->
                        <div v-if="loading" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-3 text-muted">Loading invoice...</p>
                        </div>

                        <!-- Invoice Template -->
                        <div v-else class="card invoice-card" id="invoice-template" ref="invoiceRef">
                            <div class="card-body p-4">
                                <!-- Invoice Header -->
                                <div class="invoice-header d-flex justify-content-between align-items-start mb-4">
                                    <div class="company-info">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="logo-circle bg-primary me-3">
                                                <i class="fas fa-dumbbell text-white"></i>
                                            </div>
                                            <div>
                                                <h3 class="mb-0 text-primary">{{ settings.gym_name || 'GYM Manager' }}</h3>
                                                <small class="text-muted">Fitness Center</small>
                                            </div>
                                        </div>
                                        <p class="mb-0 text-muted small" v-if="settings.gym_address">
                                            <i class="fas fa-map-marker-alt me-1"></i>{{ settings.gym_address }}
                                        </p>
                                        <p class="mb-0 text-muted small" v-if="settings.gym_phone">
                                            <i class="fas fa-phone me-1"></i>{{ settings.gym_phone }}
                                        </p>
                                        <p class="mb-0 text-muted small" v-if="settings.gym_email">
                                            <i class="fas fa-envelope me-1"></i>{{ settings.gym_email }}
                                        </p>
                                    </div>
                                    <div class="invoice-info text-end">
                                        <h2 class="text-uppercase mb-2" style="letter-spacing: 2px;">
                                            <span class="badge bg-dark fs-4">INVOICE</span>
                                        </h2>
                                        <p class="mb-1">
                                            <strong class="text-primary">{{ invoice.invoice_id }}</strong>
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            <i class="fas fa-calendar me-1"></i>{{ formatDate(invoice.created_at) }}
                                        </p>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <!-- Bill To Section -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="bill-to-card p-3 bg-light rounded">
                                            <h6 class="text-uppercase text-muted mb-3">
                                                <i class="fas fa-user me-2"></i>Bill To
                                            </h6>
                                            <h5 class="mb-2">{{ invoice.member?.name || '-' }}</h5>
                                            <p class="mb-1 text-muted">
                                                <i class="fas fa-id-card me-1"></i>
                                                Member ID: <strong>{{ invoice.member?.member_id }}</strong>
                                            </p>
                                            <p class="mb-1 text-muted" v-if="invoice.member?.mobile">
                                                <i class="fas fa-phone me-1"></i>{{ invoice.member?.mobile }}
                                            </p>
                                            <p class="mb-0 text-muted" v-if="invoice.member?.address">
                                                <i class="fas fa-map-marker-alt me-1"></i>{{ invoice.member?.address }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="payment-info-card p-3 bg-light rounded">
                                            <h6 class="text-uppercase text-muted mb-3">
                                                <i class="fas fa-credit-card me-2"></i>Payment Details
                                            </h6>
                                            <p class="mb-2">
                                                <span class="text-muted">Method:</span>
                                                <span class="badge ms-2" :class="invoice.payment_type === 'cash' ? 'bg-success' : 'bg-info'">
                                                    <i :class="invoice.payment_type === 'cash' ? 'fas fa-money-bill' : 'fas fa-university'" class="me-1"></i>
                                                    {{ invoice.payment_type === 'cash' ? 'Tiền mặt' : 'Chuyển khoản' }}
                                                </span>
                                            </p>
                                            <p class="mb-2">
                                                <span class="text-muted">Status:</span>
                                                <span class="badge bg-success ms-2">
                                                    <i class="fas fa-check-circle me-1"></i>PAID
                                                </span>
                                            </p>
                                            <p class="mb-0">
                                                <span class="text-muted">Period:</span>
                                                <br>
                                                <small>{{ formatDate(invoice.start_date) }} - {{ formatDate(invoice.end_date) }}</small>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Invoice Details Table -->
                                <div class="table-responsive">
                                    <table class="table table-bordered mb-0">
                                        <thead class="table-primary">
                                            <tr>
                                                <th style="width: 50px;">#</th>
                                                <th>Description</th>
                                                <th>Period</th>
                                                <th class="text-end" style="width: 150px;">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-center">1</td>
                                                <td>
                                                    <i class="fas fa-dumbbell me-2 text-primary"></i>
                                                    <strong>Gym Membership Package</strong>
                                                </td>
                                                <td>{{ formatDate(invoice.start_date) }} - {{ formatDate(invoice.end_date) }}</td>
                                                <td class="text-end fw-bold">{{ formatCurrency(invoice.amount) }}</td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr class="table-light">
                                                <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                                <td class="text-end">{{ formatCurrency(invoice.amount) }}</td>
                                            </tr>
                                            <tr class="table-light">
                                                <td colspan="3" class="text-end"><strong>Tax (0%):</strong></td>
                                                <td class="text-end">{{ formatCurrency(0) }}</td>
                                            </tr>
                                            <tr class="table-success">
                                                <td colspan="3" class="text-end"><strong class="fs-5">Total:</strong></td>
                                                <td class="text-end"><strong class="fs-5 text-success">{{ formatCurrency(invoice.amount) }}</strong></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <!-- Note -->
                                <div v-if="invoice.note" class="mt-4 p-3 bg-warning bg-opacity-10 border-start border-warning border-3 rounded">
                                    <h6 class="mb-2">
                                        <i class="fas fa-sticky-note me-2 text-warning"></i>Note
                                    </h6>
                                    <p class="mb-0 text-muted">{{ invoice.note }}</p>
                                </div>

                                <!-- Footer -->
                                <div class="mt-5 pt-4 border-top text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-heart text-danger"></i>
                                        <span class="text-muted ms-2">Thank you for your membership!</span>
                                    </div>
                                    <div class="social-links" v-if="settings.facebook_url || settings.youtube_url">
                                        <a v-if="settings.facebook_url" :href="settings.facebook_url" class="btn btn-outline-primary btn-sm me-2" target="_blank">
                                            <i class="fab fa-facebook me-1"></i>Facebook
                                        </a>
                                        <a v-if="settings.youtube_url" :href="settings.youtube_url" class="btn btn-outline-danger btn-sm" target="_blank">
                                            <i class="fab fa-youtube me-1"></i>Youtube
                                        </a>
                                    </div>
                                    <p class="text-muted small mt-3 mb-0">
                                        {{ settings.gym_name || 'GYM Manager' }} &copy; {{ new Date().getFullYear() }}
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
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useToast } from 'vue-toastification';
import api from '@/utils/axios';

export default {
    name: 'InvoiceDetail',
    setup() {
        const route = useRoute();
        const toast = useToast();
        const invoiceRef = ref(null);

        const invoice = ref({});
        const settings = ref({});
        const loading = ref(true);
        const downloading = ref(false);

        const fetchInvoice = async () => {
            loading.value = true;
            try {
                const response = await api.get(`/invoices/${route.params.id}`);
                invoice.value = response.data.data.invoice;
                settings.value = response.data.data.settings || {};
            } catch (error) {
                toast.error('Failed to fetch invoice');
            } finally {
                loading.value = false;
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
            }).format(amount || 0);
        };

        const printInvoice = () => {
            window.print();
        };

        const downloadPDF = async () => {
            downloading.value = true;
            try {
                // Dynamic import for PDF libraries
                const html2canvas = (await import('html2canvas')).default;
                const jsPDF = (await import('jspdf')).default;

                const element = invoiceRef.value;
                const canvas = await html2canvas(element, {
                    scale: 2,
                    useCORS: true,
                    logging: false,
                });

                const imgData = canvas.toDataURL('image/png');
                const pdf = new jsPDF('p', 'mm', 'a4');

                const pdfWidth = pdf.internal.pageSize.getWidth();
                const pdfHeight = (canvas.height * pdfWidth) / canvas.width;

                pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
                pdf.save(`${invoice.value.invoice_id}.pdf`);

                toast.success('PDF downloaded successfully');
            } catch (error) {
                console.error('PDF generation error:', error);
                toast.error('Failed to generate PDF. Please install html2canvas and jspdf packages.');
            } finally {
                downloading.value = false;
            }
        };

        onMounted(() => {
            fetchInvoice();
        });

        return {
            invoice,
            settings,
            loading,
            downloading,
            invoiceRef,
            formatDate,
            formatCurrency,
            printInvoice,
            downloadPDF,
        };
    },
};
</script>

<style scoped>
.invoice-card {
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    border-radius: 1rem;
}

.logo-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.bill-to-card,
.payment-info-card {
    height: 100%;
}

.table th {
    font-weight: 600;
}

@media print {
    .no-print,
    .content-header,
    .breadcrumb {
        display: none !important;
    }

    .invoice-card {
        border: none !important;
        box-shadow: none !important;
    }

    .container-fluid {
        max-width: 100% !important;
    }

    body {
        background: white !important;
    }
}
</style>
