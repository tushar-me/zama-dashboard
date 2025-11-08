<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { useToast } from "primevue/usetoast";
import moment from 'moment';
import type { OrderStatus } from '@/types';
import { watchDebounced } from '@vueuse/core';


defineOptions({ layout: AppLayout });
const toast = useToast();
const props = defineProps<{
    orderStatuses: {
        data: OrderStatus[],
        meta: {
            per_page: number,
            current_page: number,
            total: number,
        },
    },
    errors: any,
    search: string,
    limit: number,
}>();

const home = ref({
    label: 'Dashboard',
    icon: 'pi pi-home'
});
const items = ref([
    { label: 'Order Statuses' }
]);

function onPageChange(event: any) {
  const currentPage = event.page + 1;
  router.get(route("order-status.index"), {
    page: currentPage,
    limit: event.rows,
    search: search.value,
  }, {
    preserveState: true,
    preserveScroll: true,
  });
}

const search = ref<string | null>(props.search || '');
watchDebounced(
  search,
  () => {
    router.get(route('order-status.index'), { search: search.value, limit: props.limit }, { preserveState: true });
  },
  { debounce: 500 }
);


const form = useForm({
    name: '',
    description: '',
    order_level: 0,
    status: true,
    is_default: false,
});

const isDefaultBoolean = computed({
    get: () => form.is_default === 1,
    set: (value: boolean) => {
        form.is_default = value ? 1 : 0;
    }
});

const createDialog = ref(false);
const editDialog = ref(false);
const selectedOrderStatus = ref<OrderStatus | null>(null);


watch(createDialog, (newVal) => {
    if (!newVal) {
        form.reset();
        form.clearErrors();
    }
});

watch(editDialog, (newVal) => {
    if (newVal && selectedOrderStatus.value) {
        form.name = selectedOrderStatus.value.name;
        form.description = selectedOrderStatus.value.description ?? '';
        form.order_level = selectedOrderStatus.value.order_level;
        form.status = selectedOrderStatus.value.status;
        form.is_default = selectedOrderStatus.value.is_default;
    } else {
        // Reset form and clear errors when dialog closes
        form.reset();
        form.clearErrors();
    }
});

const openEditDialog = (orderStatus: OrderStatus) => {
    selectedOrderStatus.value = orderStatus;
    editDialog.value = true;
};

const onSubmit = () => {
    form.post(route('order-status.store'), {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'Order Status created successfully', life: 3000 });
            createDialog.value = false;
            form.reset(); 
        },
        onError: (errors) => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Something went wrong!', life: 3000 });
        }
    });
};

const onUpdate = () => {
    if (!selectedOrderStatus.value) return;
    form.put(route('order-status.update', selectedOrderStatus.value.id), {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'Order Status updated successfully', life: 3000 });
            editDialog.value = false;
        },
        onError: () => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Something went wrong!', life: 3000 });
        }
    });
};

// Delete
const deleteDialog = ref(false);
const openDeleteDialog = (orderStatus: OrderStatus) => {
    selectedOrderStatus.value = orderStatus;
    deleteDialog.value = true;
};

const onDelete = () => {
    if (!selectedOrderStatus.value) return;
    router.delete(route('order-status.destroy', selectedOrderStatus.value.id), {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'Order Status deleted successfully', life: 3000 });
            deleteDialog.value = false;
        },
        onError: () => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Something went wrong!', life: 3000 });
        }
    });
};

</script>

<template>
    <Head title="Order Statuses" />
    <Toast />

    <div class="border border-gray-200 rounded-2xl overflow-hidden bg-white">
        <DataTable :value="orderStatuses?.data">
            <template #header>
                <div class="flex justify-between items-center">
                   <Breadcrumb :home="home" :model="items" />
                    <div class="flex items-center gap-2">
                        <IconField>
                            <InputIcon>
                                <i class="pi pi-search" />
                            </InputIcon>
                            <InputText v-model="search" placeholder="Search" />
                        </IconField>
                        <Button @click="createDialog = true" label="Create Order Status" icon="pi pi-plus" />
                    </div>
                </div>
                <Paginator
                    :rows="orderStatuses.meta.per_page"
                    :first="(orderStatuses.meta.current_page - 1) * orderStatuses.meta.per_page"
                    :totalRecords="orderStatuses.meta.total"
                    :rowsPerPageOptions="[10, 20, 30, 50]"
                    @page="onPageChange"
                />
            </template>
            <template #empty> No order statuses found. </template>
            <template #loading> Loading order statuses data. Please wait. </template>

            <Column field="name" header="Name" sortable style="min-width: 12rem" />
            <Column field="description" header="Description" style="min-width: 15rem" />
            <Column field="is_default" header="Default?" style="min-width: 6rem">
                <template #body="{ data }">
                    <Tag :value="data.is_default ? 'Yes' : 'No'" :severity="data.is_default ? 'info' : 'secondary'" />
                </template>
            </Column>
            <Column field="created_by" header="Creator" style="min-width: 10rem">
                <template #body="{ data }">
                    <p class="text-sm font-normal">{{ data?.creator?.name }}</p>
                    <p class="text-sm font-normal">{{ moment(data?.created_at).format('MMMM Do YYYY, h:mm a') }}</p>
                </template>
            </Column>
            <Column field="last_updated_at" header="Last Updated By" style="min-width: 10rem">
                <template #body="{ data }">
                    <p class="text-sm font-normal">{{ data?.editor?.name }}</p>
                    <p class="text-sm font-normal">{{ moment(data?.updated_at).format('MMMM Do YYYY, h:mm a') }}</p>
                </template>
            </Column>
            <Column style="min-width: 10rem">
                <template #body="{ data }">
                    <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="openEditDialog(data)" />
                    <Button icon="pi pi-trash" outlined rounded severity="danger" @click="openDeleteDialog(data)" />
                </template>
            </Column>
        </DataTable>
    </div>

    <Dialog v-model:visible="createDialog" modal header="Create Order Status" :style="{ width: '40rem' }">
        <form @submit.prevent="onSubmit" class="p-3">
            <div class="flex flex-col gap-4">
                <div>
                    <label for="create-name" class="font-semibold mb-2 block">Name</label>
                    <InputText v-model="form.name" id="create-name" class="w-full" :invalid="!!errors.name" />
                    <small v-if="errors.name" class="p-error">{{ errors.name }}</small>
                </div>
                <div>
                    <label for="create-description" class="font-semibold mb-2 block">Description</label>
                    <Textarea v-model="form.description" id="create-description" rows="3" class="w-full" :invalid="!!errors.description" />
                    <small v-if="errors.description" class="p-error">{{ errors.description }}</small>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center">
                        <Checkbox v-model="isDefaultBoolean" inputId="create-is_default" binary />
                        <label for="create-is_default" class="ml-2"> Set as Default </label>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-6">
                <Button label="Cancel" text severity="secondary" @click="createDialog = false" />
                <Button type="submit" :loading="form.processing" label="Save" />
            </div>
        </form>
    </Dialog>

    <Dialog v-model:visible="editDialog" modal header="Edit Order Status" :style="{ width: '40rem' }">
        <form @submit.prevent="onUpdate" class="p-3">
            <div class="flex flex-col gap-4">
                <div>
                    <label for="edit-name" class="font-semibold mb-2 block">Name</label>
                    <InputText v-model="form.name" id="edit-name" class="w-full" :invalid="!!errors.name" />
                    <small v-if="errors.name" class="p-error">{{ errors.name }}</small>
                </div>
                <div>
                    <label for="edit-description" class="font-semibold mb-2 block">Description</label>
                    <Textarea v-model="form.description" id="edit-description" rows="3" class="w-full" :invalid="!!errors.description" />
                    <small v-if="errors.description" class="p-error">{{ errors.description }}</small>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center">
                        <Checkbox v-model="form.is_default" inputId="edit-is_default" binary />
                        <label for="edit-is_default" class="ml-2"> Set as Default </label>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-6">
                <Button label="Cancel" text severity="secondary" @click="editDialog = false" />
                <Button type="submit" :loading="form.processing" label="Update" />
            </div>
        </form>
    </Dialog>

    <Dialog v-model:visible="deleteDialog" modal header="Delete Order Status" :style="{ width: '30rem' }">
        <div class="p-4 flex flex-col items-center gap-4 text-center">
            <span class="size-16 bg-red-100 rounded-full flex items-center justify-center">
                <Icon name="qlementine-icons:warning-16" class="text-3xl text-red-500" />
            </span>
            <p>Are you sure you want to delete <span class="font-bold">{{ selectedOrderStatus?.name }}</span>? This action cannot be undone.</p>
        </div>
        <div class="flex justify-end gap-2 mt-4">
            <Button label="Cancel" text severity="secondary" @click="deleteDialog = false" />
            <Button @click="onDelete" severity="danger" label="Yes, Delete" />
        </div>
    </Dialog>

</template>