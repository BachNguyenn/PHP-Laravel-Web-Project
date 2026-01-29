<template>
    <div>
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Invoice Detail</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><router-link to="/dashboard">Home</router-link></li>
                            <li class="breadcrumb-item"><router-link to="/invoices">Invoices</router-link></li>
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
                    <div class="col-md-8">
                        <!-- Action buttons -->
                        <div class="mb-3 d-flex gap-2">
                            <router-link to="/invoices" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to List
                            </router-link>
                            <button class="btn btn-primary" @click="printInvoice">
                                <i class="fas fa-print me-2"></i>Print
                            </button>
                            <button class="btn btn-success" @click="downloadPDF" :disabled="downloading">
                                <span v-if="downloading">
                                    <i class="fas fa-spinner fa-spin me-2"></i>Generating...
                                </span>
                                <span v-else>
                                    <i class="fas fa-file-pdf me-2"></i>Download PDF
                                </span>
                            </button>
                        </div>

                        <!-- Loading -->
                        <div v-if="loading" class="text-center py-5">
                            <i class="fas fa-spinner fa-spin fa-3x"></i>
                        </div>

                        <!-- Invoice Template -->
                        <div v-else class="card" id="invoice-template" ref="invoiceRef">
                            <div class="card-body p-4">
                                <!-- Header -->
                                <div class="row mb-4">
                                    <div class="col-6">
                                        <img v-if="settings.gym_logo" :src="settings.gym_logo" alt="Logo" style="max-height: 80px;" />
                                        <h2 v-else class="text-primary mb-0">
                                            <i class="fas fa-dumbbell me-2"></i>{{ settings.gym_name || 'GYM Manager' }}
                                        </h2>
                                    </div>
                                    <div class="col-6 text-end">
                                        <h4 class="text-muted">INVOICE</h4>
                                        <p class="mb-0"><strong>{{ invoice.invoice_id }}</strong></p>
                                        <p class="mb-0">Date: {{ formatDate(invoice.created_at) }}</p>
                                    </div>
                                </div>

                                <!-- Gym Info -->
                                <div class="row mb-4">
                                    <div class="col-6">
                                        <h6 class="text-muted">From:</h6>
                                        <p class="mb-0"><strong>{{ settings.gym_name || 'GYM Manager' }}</strong></p>
                                        <p class="mb-0" v-if="settings.gym_address">{{ settings.gym_address }}</p>
                                        <p class="mb-0" v-if="settings.gym_phone">Phone: {{ settings.gym_phone }}</p>
                                        <p class="mb-0" v-if="settings.gym_email">Email: {{ settings.gym_email }}</p>
                                    </div>
                                    <div class="col-6">
                                        <h6 class="text-muted">Bill To:</h6>
                                        <p class="mb-0"><strong>{{ invoice.member?.name || '-' }}</strong></p>
                                        <p class="mb-0">Member ID: {{ invoice.member?.member_id }}</p>
                                        <p class="mb-0" v-if="invoice.member?.mobile">Phone: {{ invoice.member?.mobile }}</p>
                                        <p class="mb-0" v-if="invoice.member?.address">{{ invoice.member?.address }}</p>
                                    </div>
                                </div>

                                <!-- Invoice Details Table -->
                                <table class="table table-bordered">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Description</th>
                                            <th>Period</th>
                                            <th class="text-end">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Gym Membership Package</td>
                                            <td>{{ formatDate(invoice.start_date) }} - {{ formatDate(invoice.end_date) }}</td>
                                            <td class="text-end">{{ formatCurrency(invoice.amount) }}</td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-light">
                                            <td colspan="2" class="text-end"><strong>Total:</strong></td>
                                            <td class="text-end"><strong>{{ formatCurrency(invoice.amount) }}</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>

                                <!-- Payment Info -->
                                <div class="row mt-4">
                                    <div class="col-6">
                                        <h6>Payment Method:</h6>
                                        <p>
                                            <span class="badge" :class="invoice.payment_type === 'cash' ? 'bg-success' : 'bg-info'">
                                                {{ invoice.payment_type === 'cash' ? 'Tiền mặt' : 'Chuyển khoản' }}
                                            </span>
                                        </p>
                                    </div>
                                    <div class="col-6 text-end">
                                        <h6>Status:</h6>
                                        <span class="badge bg-success fs-6">PAID</span>
                                    </div>
                                </div>

                                <!-- Note -->
                                <div v-if="invoice.note" class="mt-4 p-3 bg-light rounded">
                                    <h6>Note:</h6>
                                    <p class="mb-0">{{ invoice.note }}</p>
                                </div>

                                <!-- Footer -->
                                <div class="mt-5 pt-4 border-top text-center text-muted">
                                    <p class="mb-1">Thank you for your membership!</p>
                                    <small v-if="settings.facebook_url || settings.youtube_url">
                                        <span v-if="settings.facebook_url">Facebook: {{ settings.facebook_url }}</span>
                                        <span v-if="settings.facebook_url && settings.youtube_url"> | </span>
                                        <span v-if="settings.youtube_url">Youtube: {{ settings.youtube_url }}</span>
                                    </small>
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
import html2canvas from 'html2canvas';
import jsPDF from 'jspdf';
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
                currency: 'VND' 
            }).format(amount || 0);
        };

        const printInvoice = () => {
            window.print();
        };

        const downloadPDF = async () => {
            downloading.value = true;
            try {
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
                toast.error('Failed to generate PDF');
            } finally {
                downloading.value = false;
            }
        };

        onMounted(() => {
            fetchInvoice();
        });

        return {
            invoice, settings, loading, downloading, invoiceRef,
            formatDate, formatCurrency, printInvoice, downloadPDF,
        };
    },
};
</script>

<style scoped>
@media print {
    .content-header,
    .btn,
    .breadcrumb {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
}
</style>
