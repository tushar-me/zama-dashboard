<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import DataChart from '@/components/SaleChart.vue';
import DashboardWidgets from '@/components/DashboardWidgets.vue';
import OrderChart from '@/components/OrderChart.vue';

defineOptions({ layout: AppLayout });
</script>

<template>
    <Head title="Dashboard" />
    <section class="max-w-7xl mx-auto">
        <DashboardWidgets />
        <DataChart />
        <OrderChart />
    </section>
</template>
