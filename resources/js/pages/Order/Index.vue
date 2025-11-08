<script setup lang="ts">
import { ref,computed, watch, reactive, onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { useToast } from "primevue/usetoast";
import moment from 'moment';
import type { Order } from '@/types';
import { watchDebounced } from '@vueuse/core'

defineOptions({ layout: AppLayout });
const toast = useToast();
const props = defineProps<{
    orders: {
        data: Order[],
        meta: {
            per_page: number,
            current_page: number,
            total: number
        },
        search: string
    },
}>()
const home = ref({
    label: 'Dashboard',
    icon: 'pi pi-home'
});
const items = ref([
    { label: 'Orders' }
]);

function onPageChange(event: any) {
  const currentPage = event.page + 1

  router.get(route("order.index"), {
    page: currentPage,
    limit: event.rows,
  }, {
    preserveState: true,
    preserveScroll: true,
  })
}

const search = ref<string | null>('');
const limit = ref(10);
const page = ref(1);
const searchInput = ref<HTMLInputElement | null>(null);

watchDebounced(
  search,
  () => {
    getOrders();
  },
  { debounce: 500 }
);
const getOrders = () => {
    router.get(route('order.index'), { search: search.value, limit: limit.value, page: page.value });
}

const selectedOrder = ref<Order | null>(null);


const show = (id?: string) => {
    router.visit(route('order.show', id));
}
// Delete Record
const visibleDeleteDialog = ref(false);
const onDelete = (id: string | undefined) => {
    router.delete(`/country/${id}`, {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'Country has been deleted successfully', life: 3000 });
            const audio = new Audio('/success.wav');
            audio.play();
            visibleDeleteDialog.value = false;
            selectedOrder.value = null;
        }
    })
}

</script>

<template>
    <Head title="Orders" />
    <Toast />
    <div class="border border-gray-200 rounded-2xl overflow-hidden bg-white">
        <DataTable :value="orders?.data">
            <template #header>
                <div class="flex justify-between items-center">
                   <Breadcrumb :home="home" :model="items" />
                    <div class="flex items-center gap-2">
                        <form @submit.prevent="getOrders">
                            <IconField>
                                <InputIcon>
                                    <i class="pi pi-search" />
                                </InputIcon>
                                <InputText v-model="search" ref="searchInput" placeholder="Search" />
                            </IconField>
                        </form>
                    </div>
                </div>
                <Paginator
                        :rows="props.orders.meta.per_page ?? 10"
                        :first="(props.orders.meta.current_page - 1) * props.orders.meta.per_page"
                        :totalRecords="props.orders.meta.total"
                        :rowsPerPageOptions="[10, 20, 30]"
                        @page="onPageChange"
                    />
            </template>
            <template #empty> No Color found. </template>
            <template #loading> Loading country data. Please wait. </template>
            <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
            <Column field="order_code" class="text-xs font-bold" header="# TRX ID" style="min-width: 5rem">
                <template #body="{ data }" >
                    <span @click="show(data?.id)" class="cursor-pointer bg-gray-100 px-2 py-1 rounded">{{ data?.order_code }}</span>
                </template>
            </Column>
            <Column field="created_at" header="Date" style="min-width: 10rem">
                <template #body="{ data }">
                    <p class="text-xs font-semibold">{{ moment(data?.created_at).format('MMMM Do YYYY, h:mm a') }}</p>
                </template>
            </Column>
            <Column field="custmer" header="Customer" style="min-width: 12rem">
                <template #body="{ data }">
                    <p class="text-xs font-normal">{{ data?.customer?.name }}</p>
                    <p class="text-xs font-normal">{{ data?.customer?.email }}</p>
                    <p class="text-xs font-normal">{{ data?.customer?.phone }}</p>
                </template>
            </Column>
            
             <Column field="payment_status" header="Payment Status" style="min-width: 10rem">
                <template #body="{ data }">
                    <p class="text-sm font-semibold capitalize">{{ data?.payment_status }}</p>
                </template>
            </Column>
            <Column field="grand_total" header="Total Amount" style="min-width: 10rem">
                <template #body="{ data }">
                    <p class="text-sm font-bold">{{ data?.grand_total}}</p>
                </template>
            </Column>
            <Column field="payment_method" header="Payment Method" style="min-width: 12rem">
                <template #body="{ data }">
                    <span class="text-sm font-medium capitalize">{{ data?.payment_method }}</span>
                    <span v-if="data?.paypal_order_id"  class="block text-xs font-medium capitalize">ID: {{ data?.paypal_order_id }}</span>
                </template>
            </Column>
           <Column field="sub_total" header="Sub Total" style="min-width: 10rem">
                <template #body="{ data }">
                    <p class="text-sm font-normal">{{ data?.sub_total }}</p>
                </template>
            </Column>
            <Column field="shipping_charge" header="Shipping Charge" style="min-width: 10rem">
                <template #body="{ data }">
                    <p class="text-sm font-normal">{{ data?.shipping_charge }}</p>
                </template>
            </Column>
            <Column field="vat_tax" header="VAT & TAX" style="min-width: 10rem">
                <template #body="{ data }">
                    <p class="font-normal text-xs">VAT {{ data?.vat }}</p>
                    <p class="font-normal text-xs">TAX {{ data?.tax }}</p>
                </template>
            </Column>
           
            <Column>
                <template #body="{ data }">
                    <div class="flex items-center">
                        <Button icon="pi pi-eye" outlined rounded class="mr-2" @click="show(data?.id)" />
                    </div>
                </template>
            </Column>
        </DataTable>
    </div>
</template>
